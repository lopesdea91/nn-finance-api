<?php

namespace App\Services;

use App\Models\FinanceItemModel;
use App\Models\FinanceWalletCompositionModel;
use App\Models\FinanceWalletConsolidationCompositionModel;
use App\Models\FinanceWalletConsolidationMonthModel;
use App\Models\FinanceWalletConsolidationOriginModel;
use App\Models\FinanceWalletConsolidationTagModel;

class FinanceWalletConsolidationMonthService
{
  private $periodCurrent;
  private $wallet_id;
  private $items = [];
  private $consolidationCurrent;
  private $consolidationBalance = [
    'revenue'   => 0, // Receitas (1)
    'expense'   => 0, // Despesas (2)
    'available' => 0, // Sobra    (1-2)   despesas (ok)
    'estimate'  => 0, // Estimado (1-2-3) despesas (not ok)
  ];

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
  ];

  public function __construct()
  {
  }

  public function consolidate($fields)
  {
    $period = $fields['period'];
    $wallet_id = $fields['wallet_id'];

    # apure period
    $p = $period;
    $explode_period = explode('-', $p);

    $this->periodCurrent  = now()->setDate($explode_period[0], $explode_period[1], 01);

    # wallet_id
    $this->wallet_id = intval($wallet_id);

    $this->createBasesConsolidation();
    $this->resetsConsolidation();
    $this->getFinanceItems();
    $this->balance();
    $this->composition();
    $this->originTransactional();
    $this->originCredit(); // refazer
    $this->tag();
    // $this->list();
    // $this->invoice();

    ## computations
    // $this->status();
  }

  private function createBasesConsolidation()
  {
    $where =  [
      'year'      => $this->periodCurrent->format('Y'),
      'month'     => $this->periodCurrent->format('m'),
      'wallet_id' => $this->wallet_id,
    ];

    $this->consolidationCurrent = FinanceWalletConsolidationMonthModel::with([
      'balance',
      'composition'
    ])
      ->where($where)
      ->first();

    ## create when result is null
    if (!$this->consolidationCurrent) {
      $this->consolidationCurrent = FinanceWalletConsolidationMonthModel::create([
        'year'      => $this->periodCurrent->format('Y'),
        'month'     => $this->periodCurrent->format('m'),
        'wallet_id' => $this->wallet_id,
      ]);
    }

    if (!$this->consolidationCurrent->balance) {
      $this->consolidationCurrent->balance()->create([
        'revenue'   => 0, // Receitas (1)
        'expense'   => 0, // Despesas (2)
        'available' => 0, // Sobra    (1-2)   despesas (ok)
        'estimate'  => 0, // Estimado (1-2-3) despesas (not ok)
      ]);
    }
  }

  private function resetsConsolidation()
  {
    // delete old origins
    FinanceWalletConsolidationOriginModel::where([
      'consolidation_id' => $this->consolidationCurrent->id
    ])->delete();
  }

  private function getFinanceItems()
  {
    $this->items = FinanceItemModel::withTrashed(false)
      ->select('id', 'value', 'date', 'origin_id', 'status_id', 'type_id', 'wallet_id', 'deleted_at')
      ->whereYear('date', $this->periodCurrent)
      ->whereMonth('date', $this->periodCurrent)
      ->whereHas('wallet', function ($q) {
        $q->select('id', 'description');
        $q->where(['id' => $this->wallet_id]);
      })
      ->orderByRaw('date desc, sort desc, id asc')
      ->with('tags')
      ->with('origin', function ($q) {
        $q->select('id', 'description');
      })
      ->get()
      ->toArray();
  }

  private function balance()
  {
    $revenueOk    = 0;
    $expenseOk    = 0;
    $expenseNotOk = 0;

    ## agrupa tags em data_consolidate_base
    foreach ($this->items as $key => $item) {
      $isOk       = $item['status_id']  === 1;
      $isReceita  = $item['type_id']    === 1;
      $value      = $item['value'];

      ## apply value
      if ($isOk && $isReceita) {
        $revenueOk += $value;
      } else if ($isOk && !$isReceita) {
        $expenseOk += $value;
      } else if (!$isOk && !$isReceita) {
        $expenseNotOk += $value;
      }
    }

    $this->consolidationBalance['revenue']   = $revenueOk;
    $this->consolidationBalance['expense']   = $expenseOk;
    $this->consolidationBalance['available'] = $revenueOk - $expenseOk;
    $this->consolidationBalance['estimate']  = $revenueOk - $expenseOk - $expenseNotOk;

    unset($key, $item, $isOk, $isReceita, $value, $revenueOk, $expenseOk, $expenseNotOk);

    ## save
    $this->consolidationCurrent->balance()->update([
      'revenue'   =>  $this->consolidationBalance['revenue'],
      'expense'   =>  $this->consolidationBalance['expense'],
      'available' =>  $this->consolidationBalance['available'],
      'estimate'  =>  $this->consolidationBalance['estimate'],
    ]);
  }

  private function composition()
  {
    // busca composição vinculada ao consolidationMonth
    $compositionCurrent = $this->consolidationCurrent->composition;

    // chave para determinar se a composicao será create ou update
    $isCopy = false;

    // quando não existir busca composição vinculada a carteira
    if ($compositionCurrent->count() === 0) {
      $isCopy = true;

      $compositionCurrent = FinanceWalletCompositionModel::where([
        'wallet_id' => $this->wallet_id,
      ])
        ->get()
        ->map(function ($value) {
          return [
            'id' => 0,
            'value_current' => 0,
            'value_limit' => 0,
            'percentage_limit' => $value->percentage_limit,
            'percentage_current' => 0,
            'tag_id' => $value->tag_id,
          ];
        });
    }

    $revenue = $this->consolidationBalance['revenue'];
    $expense = $this->consolidationBalance['expense'];

    // return when expense was not positive
    if ($expense <= 0 || $compositionCurrent->count() === 0) {
      return;
    }

    $tmp = [];
    $compositionCurrent = $compositionCurrent->toArray();

    // create array with tag_id like key
    foreach ($compositionCurrent as $item) {
      $tag_id = $item['tag_id'];

      if (!key_exists($tag_id, $tmp)) {
        $value_limit = $revenue > 0
          ? (($item['percentage_limit'] / 100) * $revenue)
          : 0;

        $tmp[$tag_id] = [
          'id'                  => $item['id'],
          'value_current'       => 0,
          'value_limit'         => $value_limit,
          'percentage_limit'    => $item['percentage_limit'],
          'percentage_current'  => 0,
          'tag_id'              => $tag_id,
        ];
      }
    }

    $compositionCurrent = $tmp;

    unset($tag_id, $itemd, $tmp);

    // apure each item
    foreach ($this->items as $item) {
      $isOk  = $item['status_id'] === 1;
      $value = $item['value'];
      $tags  = $item['tags'];

      // continue when not has tags
      if (count($tags) === 0 || !$isOk) {
        continue;
      }

      $tag = reset($tags);
      $tag_id = $tag['id'];

      if (key_exists($tag_id, $compositionCurrent)) {
        /** SUM
         * example:
         *  RECEITA 2000
         *  DESPESA 50 
         *    -- combustivel tag_id 3, 50.00
         * 
         *  # CONFIG
         *  - tag_id 3
         *  - percentage_limit 5%
         * 
         *  # RULE
         *  - value_current       50.00
         *  - value_limit         100.00  ( percentual_limit sobre a despesa )
         *  - percentage_limit    5%      ( percentual_limit )
         *  - percentage_current  0.25    ( quanto gastei sobre quanto recebi )
         */
        $compositionCurrent[$tag_id]['value_current']       = $compositionCurrent[$tag_id]['value_current'] + $value;

        $compositionCurrent[$tag_id]['percentage_current']  = $revenue > 0
          ? (($compositionCurrent[$tag_id]['value_current'] / $revenue) * 100)
          : 0;
      }
    }


    // save each new composition value
    foreach ($compositionCurrent as $key => $item) {
      // when copy create new
      if ($isCopy) {
        FinanceWalletConsolidationCompositionModel::create([
          'value_current'       => $item['value_current'],
          'value_limit'         => $item['value_limit'],
          'percentage_limit'    => $item['percentage_limit'],
          'percentage_current'  => $item['percentage_current'],
          "tag_id"              => $item['tag_id'],
          'consolidation_id'    => $this->consolidationCurrent->id,
        ]);
      }
      // when not is copy update
      else {
        FinanceWalletConsolidationCompositionModel::find($item['id'])
          ->update([
            'value_current'       => $item['value_current'],
            'value_limit'         => $item['value_limit'],
            'percentage_limit'    => $item['percentage_limit'],
            'percentage_current'  => $item['percentage_current'],
          ]);
      }

      unset($compositionCurrent[$key]);
    }

    unset($compositionCurrent, $key, $item, $result);
  }

  private function tag()
  {
    $items = $this->items;

    ## create tag_key and tag_descriptions
    foreach ($items as $key => $item) {
      ## tags_ids
      $items[$key]['tags_ids'] = array_map(function ($tag) {
        return $tag['id'];
      }, $item['tags']);

      ## tag_key
      $items[$key]['tag_key'] = join(
        '.',
        array_map(function ($tag) {
          return $tag['id'];
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

    unset($key, $item);

    $data = [];

    ## agrupa TAG em data
    foreach ($items as $key => $item) {
      $isOk     = $item['status_id'] === 1;

      if ($isOk) {
        ## tag key unique
        $tag_key  = $item['tag_key'];

        ## if not exit TAG in data
        if (!key_exists($tag_key, $data)) {
          $data[$tag_key] = [
            "sum"       => 0,
            "tags_ids"  => $item['tags_ids'],
            "type_id"   => $item['type_id']
          ];

          ksort($data);
        }
        ## sum value of the TAG
        $data[$tag_key]['sum'] += $item['value'];
      }
    }

    unset($items, $key, $item, $isOk, $tag_key);

    $data = array_values($data);

    // delete all olds tags 
    $oldTags = FinanceWalletConsolidationTagModel::where([
      'consolidation_id' => $this->consolidationCurrent->id
    ])->get();

    foreach ($oldTags as $key => $item) {
      $item->tags()->sync([]); // remove tags-sync
      $item->delete(); // remove tag
    }

    // create new tags
    foreach ($data as $key => $item) {
      $result = FinanceWalletConsolidationTagModel::create([
        'sum' => $item['sum'],
        'type_id' => $item['type_id'],
        'consolidation_id' => $this->consolidationCurrent->id
      ]);

      $result->tags()->sync([]);
      $result->tags()->sync($item['tags_ids']);
    }
  }

  private function originTransactional()
  {
    $originCreditCard = 2;

    $itemsOriginTrasactionals = array_filter($this->items, function ($item) use ($originCreditCard) {
      return $item['status_id'] === 1 && $item['origin_id'] !== $originCreditCard;
    });

    $origin_ids = [];
    $data = [];

    foreach ($itemsOriginTrasactionals as $key => $item) {
      $isReceita          = $item['type_id'] === 1;
      $origin_id          = $item['origin_id'];

      ## add when not exit ORIGIN in data
      if (!key_exists($origin_id, $data)) {
        $data[$origin_id] = [
          'sum'       => 0,
          'revenue'   => 0,
          'expense'   => 0,
          'average'   => 0,
          "origin_id" => $origin_id,
        ];

        $origin_ids[] = $origin_id;
      }

      if ($isReceita) {
        $data[$origin_id]['revenue'] += $item['value'];
        $data[$origin_id]['sum'] += $item['value'];
      } else {
        $data[$origin_id]['expense'] += $item['value'];
        $data[$origin_id]['sum'] -= $item['value'];
      }

      unset($itemsOriginTrasactionals[$key]);
    }
    unset($itemsOriginTrasactionals, $key, $item, $isReceita, $origin_id);

    // create new origins
    foreach ($data as $key => $item) {
      FinanceWalletConsolidationOriginModel::create([
        'sum'       => $item['sum'],
        'revenue'   => $item['revenue'],
        'expense'   => $item['expense'],
        'average'   => $item['average'],
        "origin_id" => $item['origin_id'],
        'consolidation_id' => $this->consolidationCurrent->id
      ]);

      unset($data[$key]);
    }

    unset($data, $key, $item);
  }

  private function originCredit()
  {
    $originCreditCard = 2;

    $itemsOriginCredits = array_filter($this->items, function ($item) use ($originCreditCard) {
      return $item['status_id'] === 1 && $item['origin_id'] === $originCreditCard;
    });

    $origin_ids = [];
    $data = [];

    foreach ($itemsOriginCredits as $key => $item) {
      $origin_id = $item['origin_id'];

      ## if not exit ORIGIN in data
      if (!key_exists($origin_id, $data)) {
        $data[$origin_id] = [
          'sum'         => 0,
          "origin_id" => $origin_id,
        ];

        $origin_ids[] = $origin_id;
      }

      $data[$origin_id]['sum'] += $item['value'];
    }

    // create new origins
    foreach ($data as $key => $item) {
      FinanceWalletConsolidationOriginModel::create([
        'sum'       => $item['sum'],
        "origin_id" => $item['origin_id'],
        'consolidation_id' => $this->consolidationCurrent->id
      ]);

      unset($data[$key]);
    }

    unset($data, $key, $item);
  }

  // old idea
  // private function status()
  // {
  //   foreach ($this->items as $key => $item) {
  //     $statusId           = $item['status']['id'];
  //     $typeId             = $item['type']['id'];
  //     $statusDescription  = $item['status']['description'];
  //     $typeDescription    = $item['type']['description'];

  //     $aliasId = "{$typeId}.{$statusId}";

  //     ## if not exit STATUS in data_consolidate_base
  //     if (!key_exists($aliasId, $this->data_consolidate_base['status'])) {
  //       $this->data_consolidate_base['status'][$aliasId] = [
  //         "description" => "{$typeDescription} {$statusDescription}",
  //         'status_id'   => $statusId,
  //         'type_id'     => $typeId,
  //         'count'       => 0,
  //       ];

  //       ksort($this->data_consolidate_base['status']);
  //     }

  //     $this->data_consolidate_base['status'][$aliasId]['count']++;
  //   }

  //   $this->data_consolidate_base['status'] = array_values($this->data_consolidate_base['status']);

  //   unset($key, $item, $statusId,  $typeId, $statusDescription, $typeDescription, $aliasId);
  // }

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
}
