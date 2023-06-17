<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\{
	Auth,
	Artisan,
	DB,
	Hash
};

class AuthController extends Controller
{
	public function signUp(Request $request)
	{
		$request->validate([
			'name'      => 'required',
			'email'     => 'required',
			'password'  => 'required',
		]);

		$fields = $request->only(['name', 'email', 'password']);

		try {
			$existByEmail = !!UserModel::where(["email" => $fields['email']])->count();

			if (!$existByEmail) {
				$signUp =  UserModel::create([
					'name'     => $fields['name'],
					'email'    => $fields['email'],
					'password' => Hash::make($fields['password']),
					'type'     => 'client',
				]);

				$sts = Response::HTTP_CREATED;
				$rtn = [
					'user' => new UserResource($signUp),
				];
			} else {
				$sts = Response::HTTP_CREATED;
				$rtn = ['message' => 'email já cadastrado'];
			}
		} catch (\Exception  $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function signIn(Request $request)
	{
		$request->validate([
			'email'     => 'required',
			'password'  => 'required',
		]);

		$fields = $request->only(['email', 'password']);

		try {
			$existByEmail = !!UserModel::where(["email" => $fields['email']])->count();

			$attempt = Auth::attempt($fields);

			if (!$existByEmail || !$attempt) {
				$sts = Response::HTTP_MOVED_PERMANENTLY;
				$rtn = [
					'message' => 'Email ou senha invalidos!'
				];
			} else {
				// Artisan::call('migrate');
				// Artisan::call('migration');
				// Artisan::call('db:seed');

				$authUser   = Auth::user();

				$rtn =  [
					"token" 	=> $authUser->createToken($authUser->email)->plainTextToken,
				];
				$sts = Response::HTTP_CREATED;
			}
		} catch (\Exception  $e) {

			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}

	public function signOut()
	{
		try {
			$check = Auth::check();

			if ($check) {
				$AuthUser = Auth::user();

				$AuthUser->tokens->each(function ($token) {
					$token->delete();
				});

				$rtn = ['message' => 'success'];
			} else {
				$rtn = null;
			}
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Exception  $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}
