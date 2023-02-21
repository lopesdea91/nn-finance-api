<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Repository\FinanceItemRepository;
use App\Services\Base\BaseService;
use App\Services\FinanceWalletConsolidateMonthService;

class FinanceItemService extends BaseService
{
  protected $repository;
  protected $walletConsolidate;

  public function __construct()
  {
    $this->repository = new FinanceItemRepository;
  }
  public function paginate($args)
  {
    $user_id = Auth::user()->id;
    $tag_ids = key_exists('tag_ids', $args['query']) ? $args['query']['tag_ids'] : false;

    return $this->repository->paginate([
      'query' => $args['query'],
      'where' => $args['where'],
      'whereHas' => [
        'wallet' => function ($q) use ($user_id) {
          $q->where('user_id', $user_id);
        },
        'tags' => function ($q) use ($tag_ids) {
          if ($tag_ids) {
            $q->whereIn('tag_id', $tag_ids);
          }
        }
      ],
    ]);
  }
  public function all($args)
  {
    $user_id = Auth::user()->id;

    return parent::all([
      'query' => $args['query'],
      'where' => array_merge(
        $args['where'],
        [],
      ),
      'whereHas' => array_merge(
        $args['whereHas'],
        [
          'wallet' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
          }
        ]
      ),
    ]);
  }
  public function store($fields)
  {
    $repeatTimes = false;
    $repeatMonths = false;
    $isRepeat = $fields['repeat'] === 'REPEAT';

    // if ($isRepeat) {
    //   if (!key_exists('repeat_options', $fields))
    //     throw new ApiExceptionResponse("a chave repeat_options é obrigatório quando cadastrar um ITEM como REPEAT");

    //   $repeatTimes = key_exists('for_times', $fields['repeat_options']);
    //   $repeatMonths = key_exists('until_month', $fields['repeat_options']);

    //   if (!$repeatTimes && !$repeatMonths)
    //     throw new ApiExceptionResponse("quando cadastrar um ITEM como REPEAT é obrigatório usar em repeat_options a key for_times ou until_month ");

    // if (!$repeatTimes && !!$repeatMonths)
    //   throw new ApiExceptionResponse("a chave for_times é obrigatório em repeat_options quando cadastrar um ITEM como REPEAT");

    // if ($repeatTimes && !$repeatMonths)
    //   throw new ApiExceptionResponse("a chave until_month é obrigatório em repeat_options quando cadastrar um ITEM como REPEAT");
    // }

    $store = $this->repository->create($fields);

    $store->tags()->sync([]);
    $store->tags()->sync($fields['tag_ids']);

    $store->obs()->create([
      'obs'     => $fields['obs'],
      'item_id' => $store->id
    ]);

    if ($isRepeat) {
      if ($repeatTimes) {
        // fazer logica usando for na chave 'for_times'
      }
      if ($repeatMonths) {
        // fazer logica usando for incrementando a mês a mês até chega o mês na chave until_month
      }
    }

    (new FinanceWalletConsolidateMonthService)->consolidate([
      'period'    => $fields['date'],
      'wallet_id' => $fields['wallet_id'],
    ]);

    return $store;
  }
  public function update($id, $fields)
  {
    $where = [
      'id' => $id,
    ];

    $update = $this->repository->update($where, $fields);

    $update->tags()->sync([]);
    $update->tags()->sync($fields['tag_ids']);

    if ($update->obs) {
      $update->obs()->update([
        'obs'     => $fields['obs'],
      ]);
    } else {
      $update->obs()->create([
        'obs'     => $fields['obs'],
        'item_id' => $update->id
      ]);
    }

    // update consolidate
    (new FinanceWalletConsolidateMonthService)->consolidate([
      'period'    => $fields['date'],
      'wallet_id' => $fields['wallet_id'],
    ]);


    return $update;
  }

  public function status($id, $statusId)
  {
    $where = [
      'id' => $id,
    ];

    $update = $this->repository->update($where, [
      "status_id"  => $statusId
    ]);

    // update consolidate
    (new FinanceWalletConsolidateMonthService)->consolidate([
      'period'    => $update->date,
      'wallet_id' => $update->wallet_id,
    ]);

    return $update;
  }
}
