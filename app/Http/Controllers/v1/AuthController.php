<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{
    AuthSignUpRequest,
    AuthSignInRequest,
};
use App\Http\Resources\Auth\{
    AuthSignUpResource,
    AuthSignInResource,
};
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\{
    Auth,
    Artisan,
    DB
};

class AuthController extends Controller
{
    public function signUp(AuthService $authService, AuthSignUpRequest $authSignUpRequest)
    {
        try {
            $signUp = $authService::signUp($authSignUpRequest);

            $sts = Response::HTTP_CREATED;
            $rtn = [
                'user' => new AuthSignUpResource($signUp),
            ];
        } catch (\Exception  $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function signIn(AuthService $authService, AuthSignInRequest $authSignInRequest)
    {
        Artisan::call('migrate');
        dd('oi');
        try {
            $fields = $authSignInRequest->only('email', 'password');

            $attempt = Auth::attempt($fields);

            if ($attempt) {
                Artisan::call('migrate');
                Artisan::call('migration');
                Artisan::call('db:seed');


                $signUp = $authService::signIn();

                $user  = $signUp['user'];
                $token = $signUp['token'];

                $sts = Response::HTTP_CREATED;
                $rtn = new AuthSignInResource([
                    'user_name' => $user->name,
                    'token' => $token
                ]);
            } else {

                $sts = Response::HTTP_MOVED_PERMANENTLY;
                $rtn = ['message' => 'Email ou senha invalidos!'];
            }

            return response()->json($rtn, $sts);
        } catch (\Exception  $e) {
            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function signOut(AuthService $authService)
    {
        try {
            $check = Auth::check();

            if ($check) {

                $authService::signOut();

                $rtn = ['message' => 'success'];
            } else {
                $rtn = null;
            }
            $sts = Response::HTTP_NO_CONTENT;
        } catch (\Exception  $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }
}
