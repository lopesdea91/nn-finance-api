<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\UserResource;
use App\Models\UserModel;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController
{
	public function data()
	{
		try {
			$authUser = Auth::user();

			$user = UserModel::find($authUser->id);

			$rtn =  [
				'period' 	=> '2023-04', // now()->format('Y-m'),
				'user'    => new UserResource($user),
			];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}

	public function updateData(Request $request)
	{
		$request->validate([
			"name" 	=> 'required|string',
			"email" => 'required|string',
		]);

		$user = Auth::user();

		try {
			$fields = $request->only(['name', 'email']);

			UserModel::find($user->id)
				->update([
					'name' => $fields['name'],
					'email' => $fields['email'],
				]);

			$rtn = ['message' => 'Dados atualizados'];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}

	public function updateSecury(Request $request)
	{
		$request->validate([
			"password" => 'required',
		]);

		$user = Auth::user();

		try {
			$fields = $request->only(['password']);

			UserModel::find($user->id)
				->update([
					'password' => Hash::make($fields['password'])
				]);

			$rtn = ['message' => 'Senha atualizada'];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}
