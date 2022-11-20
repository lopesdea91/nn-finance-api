<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Services\FinanceService;

class UserService
{
    static function data()
    {
        $user = Auth::user();
        $user_id = $user->id;

        $data = [];

        $data['period'] = now()->format('Y-m');
        $data['user'] = new UserResource($user);
        $data['finance'] = (new FinanceService)::data($user_id);

        return $data;

        // $data['finance'] = $this->financeService::data($user_id);

        // $sts = Response::HTTP_OK;
        // $rtn = $data;

        // return response()->json($rtn, $sts);
    }
}
