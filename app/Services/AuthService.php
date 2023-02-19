<?php

namespace App\Services;

use App\Http\Requests\Auth\{AuthSignUpRequest};
use App\Http\Resources\Auth\AuthSignUpResource;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\{
	Auth,
	// Artisan,
	DB
};

class AuthService
{
	public function signIn()
	{
		$user   = Auth::user();

		// remove old token
		// DB::table('personal_access_tokens')->where('name', $user->email)->delete();
		// $user->tokens->each(function ($token) {
		//     $token->delete();
		// });

		$token  =  $user->createToken($user->email)->plainTextToken;

		return [
			"token" 	=> $token,
			// "user"  	=> $user,
			// 'period' 	=> now()->format('Y-m')
		];
	}

	public function signOut()
	{
		$user = Auth::user();

		$user->tokens->each(function ($token) {
			$token->delete();
		});
	}
}
