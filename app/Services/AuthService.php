<?php

namespace App\Services;

use App\Http\Requests\Auth\{AuthSignInRequest, AuthSignUpRequest, AuthSignRequest};
use App\Http\Resources\Auth\AuthSignUpResource;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Artisan,
    // DB
};

class AuthService
{
    static function signUp(AuthSignUpRequest $request)
    {
        $fields = $request->all();
        $fields['type'] = 'client';

        $create = UserRepository::create($fields);

        return new AuthSignUpResource($create);
    }

    static function signIn()
    {
        $user   = Auth::user();
        $token  =  $user->createToken($user->email)->plainTextToken;

        // remove old token
        // DB::table('personal_access_tokens')->where('name', $user->email)->delete();
        // $user->tokens->each(function ($token) {
        //     $token->delete();
        // });

        return [
            "user"  => $user,
            "token" => $token,
        ];
    }

    static function signOut()
    {
        $user = Auth::user();

        $user->tokens->each(function ($token) {
            $token->delete();
        });
    }
}
