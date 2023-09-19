<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\WalletComposition\FinanceWalletConsolidationCompositionResource;
use App\Http\Resources\Finance\WalletComposition\FinanceWalletConsolidationOriginCreditResource;
use App\Http\Resources\Finance\WalletComposition\FinanceWalletConsolidationOriginTransactionalResource;
use App\Models\FinanceWalletConsolidationBalanceModel;
use App\Models\FinanceWalletConsolidationCompositionModel;
use App\Models\FinanceWalletConsolidationMonthModel;
use App\Models\FinanceWalletConsolidationOriginModel;
use App\Services\FinanceWalletConsolidationMonthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceWalletConsolidationController
{
  public function processedMonth(Request $request, FinanceWalletConsolidationMonthService $financeWalletConsolidationMonthService)
  {
    $request->validate([
      'period'    => 'required|string',
      'wallet_id' => 'required|integer',
    ]);

    try {
      $fields = $request->only(['period', 'wallet_id']);

      $explode_period = explode('-', $fields['period']);

      $result = FinanceWalletConsolidationMonthModel::where([
        'year' => $explode_period[0],
        'month' => $explode_period[1],
        'wallet_id' => $fields['wallet_id'],
      ])->first();

      if (!$result) {
        $financeWalletConsolidationMonthService->consolidate($fields);

        $result = FinanceWalletConsolidationMonthModel::where([
          'year' => $explode_period[0],
          'month' => $explode_period[1],
          'wallet_id' => $fields['wallet_id'],
        ])->first();
      }

      if ($result) {
        $rtn = [
          'consolidation_id'  => $result->id,
        ];
        $sts = Response::HTTP_OK;
      } else {
        $sts = Response::HTTP_NO_CONTENT;
      }
    } catch (\Throwable $e) {
      $sts = Response::HTTP_FAILED_DEPENDENCY;
      $rtn = ['message' => $e->getMessage()];
    }

    return response()->json($rtn, $sts);
  }
  public function processedMonthData(Request $request)
  /** GET */
  {
    $request->validate([
      'consolidation_id' => 'required|exists:finance_wallet_consolidation_month,id',
      'wallet_id' => 'required|exists:finance_wallet,id',
      'data' => 'required|string'
    ]);

    try {
      $fields = $request->only(['consolidation_id', 'wallet_id', 'data']);
      $type_data = $fields['data'];

      if (!in_array($type_data, ['balance', 'composition', 'origin_transactional', 'origin_credit']))
        throw new ApiExceptionResponse("data invalido!");

      $consolidation_id = $fields['consolidation_id'];

      $rtn = [];

      $result = FinanceWalletConsolidationMonthModel::where([
        'wallet_id' => $fields['wallet_id']
      ])->find($consolidation_id);

      if (!$result)
        throw new ApiExceptionResponse("consolidation: id ($consolidation_id) não existe!");

      if ($type_data === 'balance') {
        $rtn = FinanceWalletConsolidationBalanceModel::where([
          'consolidation_id' => $consolidation_id
        ])->first();
      }

      if ($type_data === 'composition') {
        $rtn = FinanceWalletConsolidationCompositionResource::collection(
          FinanceWalletConsolidationCompositionModel::with('tag:id,description')->where([
            'consolidation_id' => $consolidation_id
          ])->get()
        );
      }

      if ($type_data === 'origin_transactional') {
        $rtn = FinanceWalletConsolidationOriginTransactionalResource::collection(
          FinanceWalletConsolidationOriginModel::with('origin:id,description')
            ->where([
              'consolidation_id' => $consolidation_id
            ])
            ->whereHas('origin', function ($q) {
              $q->where('type_id', '!=', 2);
            })
            ->get()
        );
      }

      if ($type_data === 'origin_credit') {
        $rtn = FinanceWalletConsolidationOriginCreditResource::collection(
          FinanceWalletConsolidationOriginModel::with('origin:id,description')
            ->where([
              'consolidation_id' => $consolidation_id
            ])
            ->whereHas('origin', function ($q) {
              $q->where('type_id', '=', 2);
            })
            ->get()
        );
      }

      $sts = Response::HTTP_OK;
    } catch (\Throwable $e) {
      $sts = Response::HTTP_FAILED_DEPENDENCY;
      $rtn = ['message' => $e->getMessage()];
    }
    return response()->json($rtn, $sts);
  }
  public function processMonth(Request $request, FinanceWalletConsolidationMonthService $financeWalletConsolidationMonthService)
  /** RUN PROCESS DATA */
  {
    $request->validate([
      'period' => 'required|string',
      'wallet_id' => 'required|integer',
    ]);

    try {
      $fields = $request->only(['period', 'wallet_id']);

      $financeWalletConsolidationMonthService->consolidate($fields);

      $rtn = ['message' => 'OK'];
      $sts = Response::HTTP_OK;
    } catch (\Throwable $e) {
      $rtn = ['message' => $e->getMessage()];
      $sts = Response::HTTP_FAILED_DEPENDENCY;
    }

    return response()->json($rtn, $sts);
  }
  public function createMonthComposition(Request $request)
  {
    $request->validate([
      'consolidation_id' => 'required|exists:finance_wallet_consolidation_month,id',
      'composition' => 'required',
    ]);

    try {
      $fields = $request->only([
        'consolidation_id',
        'composition',
      ]);

      foreach ($fields['composition'] as $value) {
        FinanceWalletConsolidationCompositionModel::updateOrCreate([
          'tag_id'             => $value['tag_id'],
          'consolidation_id'   => $fields['consolidation_id'],
        ], [
          'value_current'      => 0,
          'value_limit'        => 0,
          'percentage_limit'   => $value['percentage_limit'],
          'percentage_current' => 0,
          'tag_id'             => $value['tag_id'],
          'consolidation_id'   => $fields['consolidation_id'],
        ]);
      }

      $rtn = ['message' => "Composição criada!"];
      $sts = Response::HTTP_CREATED;
    } catch (\Throwable $e) {
      $rtn = ['message' => $e->getMessage()];
      $sts = Response::HTTP_FAILED_DEPENDENCY;
    }

    return response()->json($rtn, $sts);
  }
  public function updateMonthComposition(Request $request)
  {
    $request->validate([
      'consolidation_id' => 'required|exists:finance_wallet_consolidation_month,id',
      'composition' => 'required',
    ]);

    $fields = $request->only([
      'consolidation_id',
      'composition',
    ]);

    try {
      foreach ($fields['composition'] as $value) {
        $result = FinanceWalletConsolidationCompositionModel::where([
          'id' => $value['id'],
          'consolidation_id' => $fields['consolidation_id'],
        ])->first();

        if ($result) {
          $newFields = [];

          if (key_exists('percentage_limit',  $value))  $newFields['percentage_limit'] = $value['percentage_limit'];
          if (key_exists('tag_id',            $value))  $newFields['tag_id']           = $value['tag_id'];

          $result->update($newFields);
        }
      }

      $rtn = ['message' => "Composição atualizada!"];
      $sts = Response::HTTP_CREATED;
    } catch (\Throwable $e) {
      $rtn = ['message' => $e->getMessage()];
      $sts = Response::HTTP_FAILED_DEPENDENCY;
    }

    return response()->json($rtn, $sts);
  }
  public function deleteMonthComposition($id, Request $request)
  {
    $request->validate([
      'consolidation_id' => 'required|exists:finance_wallet_consolidation_month,id',
    ]);

    $fields = $request->only([
      'consolidation_id',
    ]);

    $result = FinanceWalletConsolidationCompositionModel::where([
      'consolidation_id' => $fields['consolidation_id'],
    ])->find($id);

    if (!$result)
      throw new ApiExceptionResponse("consolidation: id ($id) não existe!");

    try {
      $rtn = $result->delete();
      $sts = Response::HTTP_NO_CONTENT;
    } catch (\Throwable $e) {
      $rtn = ['message' => $e->getMessage()];
      $sts = Response::HTTP_FAILED_DEPENDENCY;
    }

    return response()->json($rtn, $sts);
  }
  public function processedYearData(Request $request)
  {
    $request->validate([
      'consolidation_id' => 'required|exists:finance_wallet_consolidation_month,id',
      'wallet_id' => 'required|exists:finance_wallet,id',
      'period' => 'required|string',
      'data' => 'required|string',
    ]);

    try {
      $fields = $request->only(['consolidation_id', 'wallet_id', 'data', 'period']);

      $type_data = $fields['data'];

      if (!in_array($type_data, ['balance']))
        throw new ApiExceptionResponse("data invalido!");

      $explode_period = explode('-', $fields['period']);

      $rtn = [];

      if ($type_data === 'balance') {
        // create array months of data object
        for ($i = 1; $i <= 12; $i++) {
          $rtn[$i] = [
            'label' => now()->setDate(2023, $i, 01)->format('m/Y'),
            'month' => $i,
            'balance' => [
              'revenue' => 0,
              'expense' => 0,
            ]
          ];
          ksort($rtn);
        }

        // get all month by year
        $result = FinanceWalletConsolidationMonthModel::with('balance:revenue,expense,consolidation_id')->where([
          'wallet_id' => $fields['wallet_id'],
          'year' => $explode_period[0],
        ]);

        if ($result->count()) {
          foreach ($result->get()->toArray() as $value) {
            $month = $value['month'];

            $rtn[$month]['balance']['revenue'] = $value['balance']['revenue'];
            $rtn[$month]['balance']['expense'] = $value['balance']['expense'];
          }
        }

        $rtn = array_values($rtn);

        unset($i, $date, $result, $value, $month);
      }

      $sts = Response::HTTP_OK;
    } catch (\Throwable $e) {
      $sts = Response::HTTP_FAILED_DEPENDENCY;
      $rtn = ['message' => $e->getMessage()];
    }
    return response()->json($rtn, $sts);
  }
}
