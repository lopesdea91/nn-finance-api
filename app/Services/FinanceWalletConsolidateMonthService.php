<?php

namespace App\Services;

use App\Repository\FinanceWallerConsolidateMonthRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Auth;

class FinanceWalletConsolidateMonthService extends BaseService
{
  protected $repository;
  private $periodCurrent;
  private $wallet_id;
  private $items = [];
  public $data_consolidate_base = [
    'balance' => [
      'expense'   => [ // despesas
        'value' => 0,
        // 'ok'      => [
        //   'value'   => 0,
        //   'percent' => 0,
        // ],
        // 'pending' => [
        //   'value'   => 0,
        //   'percent' => 0,
        // ],
      ],
      'revenue'   => [ // saldo
        'value' => 0,
        // 'ok'      => [
        //   'value'   => 0,
        //   'percent' => 0,
        // ],
        // 'pending' => [
        //   'value'   => 0,
        //   'percent' => 0,
        // ],
      ],
      'available' => 0, // saldo - despesas (ok)
      'estimate'  => 0, // saldo - despesas (not ok)
    ],
    'tag'    => [],
    'origin'   => [],
    'invoice'   => [],
    // 'group'    => [],
    // 'category' => [],
  ];

  public function __construct()
  {
    $this->repository = new FinanceWallerConsolidateMonthRepository;
  }

  public function consolidate($query)
  {
    $p = $query['period'];
    $explode_period = explode('-', $p);

    $this->periodCurrent  = now()->setDate($explode_period[0], $explode_period[1], 01);
    $this->wallet_id      = intval($query['wallet_id']);

    $this->getItems();
    $this->groupingItemsByTag();
    $this->saveData();
  }

  private function getItems()
  {
    $user_id = Auth::user()->id;
    $wallet_id = $this->wallet_id;

    $this->items = (new FinanceItemService)->query([
      'query' => [],
      'where' => [
        'enable' => 1,
      ],
      'whereHas' => [
        'wallet' => function ($q) use ($user_id, $wallet_id) {
          $q->where([
            'id'      => $wallet_id,
            'user_id' => $user_id
          ]);
        },
        // 'tags' => function ($q) use ($user_id) {
        //   $q->select("finance_item_tag.id", "finance_item_tag.description");
        // },
      ],
    ])
      ->whereYear('date', $this->periodCurrent)
      ->whereMonth('date', $this->periodCurrent)
      ->orderByRaw('date desc, sort desc, id asc')
      ->with('tags') // ['wallet', 'origin', 'type', 'status', 'obs', 'tags'];
      ->get()
      ->toArray();
  }

  private function groupingItemsByTag()
  {
    ## tag_key tag_descriptions
    foreach ($this->items as $key => $item) {
      ## tag_key
      $this->items[$key]['tag_key'] = join(
        '.',
        array_map(function ($tag) {
          return $tag['id'];
        }, $item['tags'])
      );

      ## tag_descriptions
      $this->items[$key]['tag_descriptions'] = join(
        ' > ',
        array_map(function ($tag) {
          return $tag['description'];
        }, $item['tags'])
      );

      ## format tags
      $this->items[$key]['tags'] =
        array_map(function ($tag) {
          return [
            "id" => $tag['id'],
            "description" => $tag['description'],
          ];
        }, $item['tags']);
    }

    $revenueOk = 0;
    $expenseOk = 0;
    $expenseNotOk = 0;

    ## agrupa tags em data_consolidate_base
    foreach ($this->items as $key => $item) {
      $isOk               = $item['status_id'] === 1;
      $isReceita          = $item['type_id'] === 1;
      $value              = $item['value'];
      $tag_descriptions   = $item['tag_descriptions'];
      $origin_description = $item['origin']['description'];

      ## if not exit tag in data_consolidate_base
      if (!key_exists($tag_descriptions, $this->data_consolidate_base['tag'])) {
        $this->data_consolidate_base['tag'][$tag_descriptions] = [
          "tag_key"           => $item['tag_key'],
          "description"       => $tag_descriptions,
          'value'             => 0,
        ];

        ksort($this->data_consolidate_base['tag']);
      }

      ## if not exit origin in data_consolidate_base
      if (!key_exists($origin_description, $this->data_consolidate_base['origin'])) {
        $this->data_consolidate_base['origin'][$origin_description] = [
          "id"                => $item['origin']['id'],
          "description"       => $origin_description,
          'value'             => 0,
        ];

        ksort($this->data_consolidate_base['origin']);
      }

      ## apply value / sum value in tag, origin
      if ($isOk) {
        if ($isReceita) {
          $revenueOk += $value;

          $this->data_consolidate_base['origin'][$origin_description]['value'] += $value;
        } else {
          $expenseOk += $value;

          $this->data_consolidate_base['origin'][$origin_description]['value'] -= $value;
        }

        $this->data_consolidate_base['tag'][$tag_descriptions]['value'] += $value;
      } else if (!$isReceita) {
        $expenseNotOk += $value;
      }
    }

    $this->data_consolidate_base['balance']['revenue']['value'] = $revenueOk;
    $this->data_consolidate_base['balance']['expense']['value'] = $expenseOk;
    $this->data_consolidate_base['balance']['available'] = $revenueOk - $expenseOk;
    $this->data_consolidate_base['balance']['estimate'] = $revenueOk - $expenseOk - $expenseNotOk;

    $this->data_consolidate_base['tag']     = array_values($this->data_consolidate_base['tag']);
    $this->data_consolidate_base['origin']  = array_values($this->data_consolidate_base['origin']);
    $this->data_consolidate_base['invoice'] = array_values($this->data_consolidate_base['invoice']);

    unset($this->items);
    unset($key, $item, $isOk, $isReceita, $value, $tag_key, $tag_descriptions, $origin_description, $revenueOk, $expenseOk, $expenseNotOk);
  }

  private function saveData()
  {
    $this->repository->createOrUpdate(
      [
        'year'      => $this->periodCurrent->format('Y'),
        'month'     => $this->periodCurrent->format('m'),
        'wallet_id' => $this->wallet_id,
      ],
      [
        'year'      => $this->periodCurrent->format('Y'),
        'month'     => $this->periodCurrent->format('m'),
        'wallet_id' => $this->wallet_id,
        'balance'   => $this->data_consolidate_base['balance'],
        'tag'       => $this->data_consolidate_base['tag'],
        'origin'    => $this->data_consolidate_base['origin'],
        'invoice'   => $this->data_consolidate_base['invoice'],
      ]
    );

    unset($this->periodCurrent, $this->wallet_id, $this->data_consolidate_base);
  }
}
