<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\{
	AuthSignInResource,
};
use App\Http\Resources\UserResource;
use App\Repository\UserRepository;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\{
	Auth,
	Artisan,
	DB
};

class AuthController extends Controller
{
	private $authService;
	private $userService;

	function __construct(AuthService $authService, UserService $userService)
	{
		$this->authService = $authService;
		$this->userService = $userService;
	}

	public function signUp(Request $request)
	{
		$request->validate([
			'name'      => 'required',
			'email'     => 'required',
			'password'  => 'required',
		]);

		$fields = $request->only(['name', 'email', 'password']);

		try {
			$signUp = $this->userService->create($fields);

			$sts = Response::HTTP_CREATED;
			$rtn = [
				'user' => new UserResource($signUp),
			];
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

		$existByEmail = $this->userService->existByEmail($fields['email']);

		try {
			if (!$existByEmail) {
				return response()->json(
					[
						'message' => 'Email não localizado!'
					],
					Response::HTTP_NOT_FOUND
				);
			}

			$attempt = Auth::attempt($fields);

			if ($attempt) {
				// Artisan::call('migrate');
				// Artisan::call('migration');
				// Artisan::call('db:seed');

				$signUp = $this->authService->signIn();

				$sts = Response::HTTP_CREATED;
				$rtn = new AuthSignInResource($signUp);
			} else {

				$sts = Response::HTTP_MOVED_PERMANENTLY;
				$rtn = [
					'message' => 'Email ou senha invalidos!'
				];
			}

			return response()->json($rtn, $sts);
		} catch (\Exception  $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function signOut()
	{
		try {
			$check = Auth::check();

			if ($check) {
				$this->authService->signOut();

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
