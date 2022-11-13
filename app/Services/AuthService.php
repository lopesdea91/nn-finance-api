<?php

namespace App\Services;

use App\Http\Requests\Auth\{AuthSignUpRequest, AuthSignRequest};
use App\Http\Resources\Auth\AuthSignUpResource;
use App\Repository\UserRepository;
use Illuminate\Http\Request;

class AuthService
{
    static function signUp(AuthSignUpRequest $request)
    {
        $fields = $request->all();
        $fields['type'] = 'client';

        $create = UserRepository::create($fields);

        return new AuthSignUpResource($create);
    }

    public function signIn(AuthSignRequest $request)
    {
        dd('signIn');
    }

    public function signOut(Request $request)
    {
        dd('signOut');
    }
}
