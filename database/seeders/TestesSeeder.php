<?php

namespace Database\Seeders;

use App\Repository\FinanceOriginRepository;
use App\Repository\FinanceTagRepository;
use App\Repository\FinanceWalletRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		(new UserRepository)->create([
			"name"      => 'teste1',
			"email"     => 'test1@email.com',
			"password"  => '1234',
		]);

		(new FinanceWalletRepository)->create([
			'description' => 'CARTEIRA TESTE',
			'json'        => '{}',
			'enable'      => 1,
			'panel'       => 1,
			'user_id'     => 1,
		]);
		(new FinanceOriginRepository)->create([
			'user_id'     => 1,
			'description' => 'BANCO INTER',
			'enable'      => 1,
			'type_id'     => 1,
			'parent_id'   => null,
			'wallet_id'   => 1,
		]);
		(new FinanceTagRepository)->create([
			'description' => 'Mercado',
			'enable'      => 1,
			'type_id'     => 2,
			'wallet_id'   => 1,
		]);
		(new FinanceTagRepository)->create([
			'description' => 'Alimentação',
			'enable'      => 1,
			'type_id'     => 2,
			'wallet_id'   => 1,
		]);
		(new FinanceTagRepository)->create([
			'description' => 'Salário',
			'enable'      => 1,
			'type_id'     => 1,
			'wallet_id'   => 1,
		]);
	}
}
