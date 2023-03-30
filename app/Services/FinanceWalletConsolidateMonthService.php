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
    'status'  => [],
    'tag'     => [],
    'origin'  => [],
    'invoice' => [],
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
    $this->balance();
    $this->status();
    $this->tag();
    $this->origin();
    // $this->list();
    // $this->invoice();
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

  private function balance()
  {
    $revenueOk = 0;
    $expenseOk = 0;
    $expenseNotOk = 0;

    ## agrupa tags em data_consolidate_base
    foreach ($this->items as $key => $item) {
      $isOk               = $item['status_id'] === 1;
      $isReceita          = $item['type_id'] === 1;
      $value              = $item['value'];

      ## apply value
      if ($isOk && $isReceita) {
        $revenueOk += $value;
      } else if ($isOk && !$isReceita) {
        $expenseOk += $value;
      } else if (!$isOk && !$isReceita) {
        $expenseNotOk += $value;
      }
    }

    $this->data_consolidate_base['balance']['revenue']['value'] = $revenueOk;
    $this->data_consolidate_base['balance']['expense']['value'] = $expenseOk;
    $this->data_consolidate_base['balance']['available'] = $revenueOk - $expenseOk;
    $this->data_consolidate_base['balance']['estimate'] = $revenueOk - $expenseOk - $expenseNotOk;

    unset($key, $item, $isOk, $isReceita, $value, $revenueOk, $expenseOk, $expenseNotOk);
  }

  private function status()
  {
    foreach ($this->items as $key => $item) {
      $statusId           = $item['status']['id'];
      $typeId             = $item['type']['id'];
      $statusDescription  = $item['status']['description'];
      $typeDescription    = $item['type']['description'];

      $aliasId = "{$typeId}.{$statusId}";

      ## if not exit STATUS in data_consolidate_base
      if (!key_exists($aliasId, $this->data_consolidate_base['status'])) {
        $this->data_consolidate_base['status'][$aliasId] = [
          "description" => "{$typeDescription} {$statusDescription}",
          'status_id'   => $statusId,
          'type_id'     => $typeId,
          'count'       => 0,
        ];

        ksort($this->data_consolidate_base['status']);
      }

      $this->data_consolidate_base['status'][$aliasId]['count']++;
    }

    $this->data_consolidate_base['status'] = array_values($this->data_consolidate_base['status']);

    unset($key, $item, $statusId,  $typeId, $statusDescription, $typeDescription, $aliasId);
  }

  private function tag()
  {
    $items = $this->items;

    ## create tag_key and tag_descriptions
    foreach ($items as $key => $item) {
      ## tag_ids
      $items[$key]['tag_ids'] = array_map(function ($tag) {
        return $tag['id'];
      }, $item['tags']);

      ## tag_key
      $items[$key]['tag_key'] = join(
        '.',
        array_map(function ($tag) {
          return $tag['id'];
        }, $item['tags'])
      );

      ## tag_descriptions
      $items[$key]['tag_descriptions'] = join(
        ' > ',
        array_map(function ($tag) {
          return $tag['description'];
        }, $item['tags'])
      );

      ## format tags
      $items[$key]['tags'] =
        array_map(function ($tag) {
          return [
            "id" => $tag['id'],
            "description" => $tag['description'],
          ];
        }, $item['tags']);
    }

    ## agrupa TAG em data_consolidate_base
    foreach ($items as $key => $item) {
      $isOk     = $item['status_id'] === 1;

      if ($isOk) {
        ## tag key unique
        $tag_key  = $item['tag_key'];

        ## if not exit TAG in data_consolidate_base
        if (!key_exists($tag_key, $this->data_consolidate_base['tag'])) {
          $this->data_consolidate_base['tag'][$tag_key] = [
            "tag_ids"         => $item['tag_ids'],
            "tag_description" => $item['tag_descriptions'],
            'sum'             => 0,
            'type_id'         => $item['type']['id']
          ];

          ksort($this->data_consolidate_base['tag']);
        }
        ## sum value of the TAG
        $this->data_consolidate_base['tag'][$tag_key]['sum'] += $item['value'];
      }
    }

    $this->data_consolidate_base['tag'] = array_values($this->data_consolidate_base['tag']);

    // unset($key, $item, $isOk, $tag_key);
  }

  private function origin()
  {
    foreach ($this->items as $key => $item) {
      $isOk               = $item['status_id'] === 1;
      $isReceita          = $item['type_id'] === 1;
      $origin_description = $item['origin']['description'];

      ## if not exit ORIGIN in data_consolidate_base
      if (!key_exists($origin_description, $this->data_consolidate_base['origin'])) {
        $this->data_consolidate_base['origin'][$origin_description] = [
          "id"                => $item['origin']['id'],
          "description"       => $origin_description,
          'sum'             => 0,
        ];

        ksort($this->data_consolidate_base['origin']);
      }

      if ($isReceita) {
        $this->data_consolidate_base['origin'][$origin_description]['sum'] += $item['value'];
      } else {
        $this->data_consolidate_base['origin'][$origin_description]['sum'] -= $item['value'];
      }
    }

    $this->data_consolidate_base['origin']  = array_values($this->data_consolidate_base['origin']);

    unset($key, $item, $isOk, $isReceita, $origin_description);
  }

  // private function groupingItemsByTag()
  // {
  //   ## tag_key tag_descriptions
  //   foreach ($this->items as $key => $item) {
  //     ## tag_key
  //     $this->items[$key]['tag_key'] = join(
  //       '.',
  //       array_map(function ($tag) {
  //         return $tag['id'];
  //       }, $item['tags'])
  //     );

  //     ## tag_descriptions
  //     $this->items[$key]['tag_descriptions'] = join(
  //       ' > ',
  //       array_map(function ($tag) {
  //         return $tag['description'];
  //       }, $item['tags'])
  //     );

  //     ## format tags
  //     $this->items[$key]['tags'] =
  //       array_map(function ($tag) {
  //         return [
  //           "id" => $tag['id'],
  //           "description" => $tag['description'],
  //         ];
  //       }, $item['tags']);
  //   }

  //   $revenueOk = 0;
  //   $expenseOk = 0;
  //   $expenseNotOk = 0;

  //   ## agrupa tags em data_consolidate_base
  //   foreach ($this->items as $key => $item) {
  //     $isOk               = $item['status_id'] === 1;
  //     $isReceita          = $item['type_id'] === 1;
  //     $value              = $item['value'];
  //     $tag_descriptions   = $item['tag_descriptions'];
  //     $origin_description = $item['origin']['description'];

  //     ## if not exit tag in data_consolidate_base
  //     if ($isOk && !key_exists($tag_descriptions, $this->data_consolidate_base['tag'])) {
  //       $this->data_consolidate_base['tag'][$tag_descriptions] = [
  //         "tag_key"           => $item['tag_key'],
  //         "description"       => $tag_descriptions,
  //         'value'             => 0,
  //         'type'             => 0,
  //       ];

  //       ksort($this->data_consolidate_base['tag']);
  //     }

  //     ## if not exit origin in data_consolidate_base
  //     if ($isOk && !key_exists($origin_description, $this->data_consolidate_base['origin'])) {
  //       $this->data_consolidate_base['origin'][$origin_description] = [
  //         "id"                => $item['origin']['id'],
  //         "description"       => $origin_description,
  //         'value'             => 0,
  //       ];

  //       ksort($this->data_consolidate_base['origin']);
  //     }

  //     ## apply value / sum value in tag, origin
  //     if ($isOk) {
  //       if ($isReceita) {
  //         $revenueOk += $value;

  //         $this->data_consolidate_base['tag'][$tag_descriptions]['type'] = 1;
  //         $this->data_consolidate_base['origin'][$origin_description]['value'] += $value;
  //       } else {
  //         $expenseOk += $value;

  //         $this->data_consolidate_base['tag'][$tag_descriptions]['type'] = 2;
  //         $this->data_consolidate_base['origin'][$origin_description]['value'] -= $value;
  //       }

  //       $this->data_consolidate_base['tag'][$tag_descriptions]['value'] += $value;
  //     } else if (!$isReceita) {
  //       $expenseNotOk += $value;
  //     }
  //   }

  //   $this->data_consolidate_base['balance']['revenue']['value'] = $revenueOk;
  //   $this->data_consolidate_base['balance']['expense']['value'] = $expenseOk;
  //   $this->data_consolidate_base['balance']['available'] = $revenueOk - $expenseOk;
  //   $this->data_consolidate_base['balance']['estimate'] = $revenueOk - $expenseOk - $expenseNotOk;

  //   $this->data_consolidate_base['tag']     = array_values($this->data_consolidate_base['tag']);
  //   $this->data_consolidate_base['origin']  = array_values($this->data_consolidate_base['origin']);
  //   $this->data_consolidate_base['invoice'] = array_values($this->data_consolidate_base['invoice']);

  //   unset($this->items);
  //   unset($key, $item, $isOk, $isReceita, $value, $tag_key, $tag_descriptions, $origin_description, $revenueOk, $expenseOk, $expenseNotOk);
  // }

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
        'status'    => $this->data_consolidate_base['status'],
        'tag'       => $this->data_consolidate_base['tag'],
        'origin'    => $this->data_consolidate_base['origin'],
        'invoice'   => $this->data_consolidate_base['invoice'],
      ]
    );

    unset($this->periodCurrent, $this->wallet_id, $this->data_consolidate_base);
  }
}
