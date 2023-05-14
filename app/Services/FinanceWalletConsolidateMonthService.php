<?php

namespace App\Services;

use App\Models\FinanceWalletConsolidateMonthModel;
use App\Repository\FinanceWallerConsolidateMonthRepository;
use App\Repository\FinanceWalletRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Auth;

class FinanceWalletConsolidateMonthService extends BaseService
{
  // 'ok'      => [
  //   'value'   => 0,
  //   'percent' => 0,
  // ],
  // 'pending' => [
  //   'value'   => 0,
  //   'percent' => 0,
  // ],
  protected $repository;
  private $periodCurrent;
  private $wallet_id;
  private $items = [];
  public $data_consolidate_base = [
    'balance' => [
      'revenue'   => 0, // Receitas (1)
      'expense'   => 0, // Despesas (2)
      'available' => 0, // Sobra    (1-2)   despesas (ok)
      'estimate'  => 0, // Estimado (1-2-3) despesas (not ok)
    ],
    'composition' => [],
    'originTransactional' => [],
    'invoice' => [],
    'tag' => [],
    'status' => [],
    // 'group'   => [],
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

    ## computations
    $this->balance();
    $this->composition();
    $this->originTransitional();
    // $this->originCredit();
    $this->status();
    $this->tag();
    // $this->list();
    // $this->invoice();

    ## formats
    $this->originTransitionalFormat();

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

    $this->data_consolidate_base['balance']['revenue']   = $revenueOk;
    $this->data_consolidate_base['balance']['expense']   = $expenseOk;
    $this->data_consolidate_base['balance']['available'] = $revenueOk - $expenseOk;
    $this->data_consolidate_base['balance']['estimate']  = $revenueOk - $expenseOk - $expenseNotOk;

    unset($key, $item, $isOk, $isReceita, $value, $revenueOk, $expenseOk, $expenseNotOk);
  }

  private function composition()
  {
    $composition = [];

    // busca composição mes
    $consolidateMonth = FinanceWalletConsolidateMonthModel::where([
      'year'      => $this->periodCurrent->format('Y'),
      'month'     => $this->periodCurrent->format('m'),
      'wallet_id' => $this->wallet_id,
    ])->first();

    // valida 'composição mes' caso não existe usa 'composição carteira'
    if ($consolidateMonth) {
      $compositionMonth = json_decode($consolidateMonth->composition, true);

      $keyCompositionHasValue = !!count($compositionMonth);

      if ($keyCompositionHasValue) {
        $composition = $compositionMonth;
      } else {
        $carteira = (new FinanceWalletRepository)->id($this->wallet_id)->toArray();

        $composition = json_decode($carteira['composition'], true);
      }
    }
    $expense = $this->data_consolidate_base['balance']['expense'];

    // return when expense was not positive
    if ($expense <= 0) {
      return;
    }

    $data_composition = [];

    // create data_composition by tag_id of item
    foreach ($composition as $item) {
      $tag_id     = $item['tag_id'];
      $percentage = $item['percentage'];

      if (!key_exists($tag_id, $data_composition)) {
        $data_composition[$tag_id] = [
          'tag_id' => $tag_id,
          'percentage' => intval($percentage),
          'percentage_current' => 0,
          'value' => 0,
        ];
      }
    }

    // apure each item
    foreach ($this->items as $item) {
      $isOk  = $item['status_id'] === 1;
      $value = $item['value'];
      $tags  = $item['tags'];

      // continue when has tags
      if (count($tags) === 0 || !$isOk) {
        continue;
      }

      $tag = reset($tags);
      $tag_id = $tag['id'];

      if (key_exists($tag_id, $data_composition)) {
        $data_composition[$tag_id]['value'] = $data_composition[$tag_id]['value'] + $value;
        $n = ($data_composition[$tag_id]['value'] / $expense) * 100;
        $data_composition[$tag_id]['percentage_current'] = number_format($n, 2, '.');
      }
    }

    $this->data_consolidate_base['composition'] = array_values($data_composition);

    unset($carteira, $composition, $expense, $data_composition, $item, $tag_id, $percentage, $isOk, $value, $tags, $tag);
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

  private function originTransitional()
  {
    $originCreditCard = 2;

    $itemsOriginTrasactionals = array_filter($this->items, function ($item) use ($originCreditCard) {
      $origin_id = $item['origin']['id'];

      return $origin_id !== $originCreditCard;
    });

    foreach ($itemsOriginTrasactionals as $key => $item) {
      $isOk               = $item['status_id'] === 1;
      $isReceita          = $item['type_id'] === 1;
      $origin_description = $item['origin']['description'];

      ## if not exit ORIGIN in data_consolidate_base
      if (!key_exists($origin_description, $this->data_consolidate_base['originTransactional'])) {
        $this->data_consolidate_base['originTransactional'][$origin_description] = [
          "id"          => $item['origin']['id'],
          "description" => $origin_description,
          'sum'         => 0,
          'revenue'     => 0,
          'expense'     => 0,
          'average'     => 0,
        ];

        ksort($this->data_consolidate_base['originTransactional']);
      }

      if ($isReceita) {
        $this->data_consolidate_base['originTransactional'][$origin_description]['revenue'] += $item['value'];
        $this->data_consolidate_base['originTransactional'][$origin_description]['sum'] += $item['value'];
      } else {
        $this->data_consolidate_base['originTransactional'][$origin_description]['expense'] += $item['value'];
        $this->data_consolidate_base['originTransactional'][$origin_description]['sum'] -= $item['value'];
      }
    }

    $this->data_consolidate_base['originTransactional']  = array_values($this->data_consolidate_base['originTransactional']);

    unset($originCreditCard, $itemsOriginTrasactionals, $origin_id, $key, $item, $isOk, $isReceita, $origin_description);
  }

  private function originTransitionalFormat()
  {
    foreach ($this->data_consolidate_base['originTransactional'] as $index => $origin) {
      $revenue = $origin['revenue'];
      $expense = $origin['expense'];

      if ($revenue > 0) {
        $this->data_consolidate_base['originTransactional'][$index]['average'] = number_format(($expense / $revenue) * 100, 2, '.');
      }
      if ($revenue == 0) {
        $this->data_consolidate_base['originTransactional'][$index]['average'] = "-" . number_format($expense, 2, '.');
      }
    }
  }

  private function originCredit()
  {
    $originCreditCard = 2;
    $itemsOriginCredits = array_filter($this->items, function ($item) use ($originCreditCard) {
      $origin_id = $item['origin']['id'];

      return $origin_id === $originCreditCard;
    });

    foreach ($itemsOriginCredits as $key => $item) {
      $origin_description = $item['origin']['description'];

      ## if not exit ORIGIN in data_consolidate_base
      if (!key_exists($origin_description, $this->data_consolidate_base['originCredit'])) {
        $this->data_consolidate_base['originCredit'][$origin_description] = [
          "id"          => $item['origin']['id'],
          "description" => $origin_description,
          'sum'         => 0,
        ];

        ksort($this->data_consolidate_base['originCredit']);
      }

      $this->data_consolidate_base['originCredit'][$origin_description]['sum'] += $item['value'];
    }

    $this->data_consolidate_base['originCredit']  = array_values($this->data_consolidate_base['originCredit']);

    unset($originCreditCard, $itemsOriginTrasactionals, $origin_id, $key, $item, $origin_description);
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
  //     if ($isOk && !key_exists($origin_description, $this->data_consolidate_base['originTransactional'])) {
  //       $this->data_consolidate_base['originTransactional'][$origin_description] = [
  //         "id"                => $item['origin']['id'],
  //         "description"       => $origin_description,
  //         'value'             => 0,
  //       ];

  //       ksort($this->data_consolidate_base['originTransactional']);
  //     }

  //     ## apply value / sum value in tag, origin
  //     if ($isOk) {
  //       if ($isReceita) {
  //         $revenueOk += $value;

  //         $this->data_consolidate_base['tag'][$tag_descriptions]['type'] = 1;
  //         $this->data_consolidate_base['originTransactional'][$origin_description]['value'] += $value;
  //       } else {
  //         $expenseOk += $value;

  //         $this->data_consolidate_base['tag'][$tag_descriptions]['type'] = 2;
  //         $this->data_consolidate_base['originTransactional'][$origin_description]['value'] -= $value;
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
  //   $this->data_consolidate_base['originTransactional']  = array_values($this->data_consolidate_base['originTransactional']);
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
        'composition' => $this->data_consolidate_base['composition'],
        'originTransactional' => $this->data_consolidate_base['originTransactional'],
        'invoice'   => $this->data_consolidate_base['invoice'],
        'tag'       => $this->data_consolidate_base['tag'],
        'status'    => $this->data_consolidate_base['status'],
      ]
    );

    unset($this->periodCurrent, $this->wallet_id, $this->data_consolidate_base);
  }
}
