<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function data(UserService $userService)
    {
        try {
            $rtn = $userService::data();
            $sts = Response::HTTP_OK;
        } catch (\Throwable $e) {
          
            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];  
        }

        return response()->json($rtn, $sts);
    }
}
