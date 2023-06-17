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
  public function processedMonth(Request $request)
  /** GET */
  {
    $request->validate([
      'period'    => 'required|string',
      'wallet_id' => 'required|integer',
      'data'      => 'required|string'
    ]);

    try {
      $fields = $request->only(['period', 'wallet_id', 'data']);
      $type_data = $fields['data'];

      if (!in_array($type_data, ['balance', 'composition', 'origin_transactional', 'origin_credit']))
        throw new ApiExceptionResponse("data invalido!");

      $explode_period = explode('-', $fields['period']);

      $rtn = [];

      $resultConsolidation = FinanceWalletConsolidationMonthModel::where([
        'year' => $explode_period[0],
        'month' => $explode_period[1],
        'wallet_id' => $fields['wallet_id'],
      ]);

      $hasContent = $resultConsolidation->count();

      if ($hasContent) {
        $content = $resultConsolidation->first();

        if ($type_data === 'balance') {
          $rtn = FinanceWalletConsolidationBalanceModel::where([
            'consolidation_id' => $content->id
          ])->first();
        }

        if ($type_data === 'composition') {
          $rtn = FinanceWalletConsolidationCompositionResource::collection(
            FinanceWalletConsolidationCompositionModel::with('tag:id,description')->where([
              'consolidation_id' => $content->id
            ])->get()
          );
        }

        if ($type_data === 'origin_transactional') {
          $rtn = FinanceWalletConsolidationOriginTransactionalResource::collection(
            FinanceWalletConsolidationOriginModel::with('origin:id,description')
              ->where([
                'consolidation_id' => $content->id
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
                'consolidation_id' => $content->id
              ])
              ->whereHas('origin', function ($q) {
                $q->where('type_id', '=', 2);
              })
              ->get()
          );
        }
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
      'consolidation_id'  => 'required|integer',
      'composition'       => 'required',
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
      'consolidation_id'  => 'required|integer',
      'composition'       => 'required',
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
}
