<?php

namespace App\Repository;

use Carbon\Carbon;

class FinanceRepository
{
    static function data($user_id)
    {
        // $monthCurrent = new Carbon();

        $data = [];
        $data['wallet']       = []; // new FinanceWalletCollection($wallet);
        $data['group']        = []; // new FinanceGroupCollection($group);
        $data['category']     = []; // new FinanceCategoryCollection($category);
        $data['origin']       = []; // new FinanceOriginCollection($origin);
        $data['type']         = []; // new FinanceOriginTypeCollection($type);
        $data['status']       = []; // $status;
        $data['originType']   = []; // $originType;
        $data['wallet_panel'] = []; // new FinanceWalletResource($wallet_panel);

        return $data;
    }
}
