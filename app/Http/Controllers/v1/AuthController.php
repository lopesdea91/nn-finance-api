<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{AuthSignUpRequest, AuthSignRequest};
use App\Services\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function signUp(AuthService $authService, AuthSignUpRequest $authSignUpRequest)
    {
        try {
            $signUp = $authService::signUp($authSignUpRequest);

            $status = Response::HTTP_CREATED;
            $content = [
                'user' => $signUp,
            ];
        } catch (\Exception  $e) {

            $status = Response::HTTP_FAILED_DEPENDENCY;
            $content = ['message' => $e->getMessage()];
        }

        return response()->json($content, $status);
    }

    public function signIn(Request $request)
    {
        dd('signIn');
    }

    public function signOut(Request $request)
    {
        dd('signOut');
    }
}
