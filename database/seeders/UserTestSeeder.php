<?php

namespace Database\Seeders;

use App\Models\FinanceItemModel;
use App\Models\FinanceOriginModel;
use App\Models\FinanceTagModel;
use App\Models\FinanceWalletModel;
use App\Models\UserModel;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{
	Auth,
	Artisan,
	DB,
	Hash
};

class UserTestSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$user = UserModel::create([
			'name'     => 'test1',
			'email'    => 'test1@email.com',
			'password' => Hash::make('123456'),
			'type'     => 'client',
		]);
		$financeWallet = FinanceWalletModel::create([
			'description' => 'teste',
			'panel' => 1,
			'user_id' => $user->id,
		]);
		FinanceTagModel::create([
			"description" => 'salário',
			"type_id" => 1,
			"wallet_id" => $financeWallet->id
		]);
		FinanceTagModel::create([
			"description" => 'alimentação',
			"type_id" => 2,
			"wallet_id" => $financeWallet->id
		]);
		FinanceTagModel::create([
			"description" => 'transporte',
			"type_id" => 2,
			"wallet_id" => $financeWallet->id
		]);

		$origin = FinanceOriginModel::create([
			"description" => 'inter Corrente',
			'type_id' 		=> 1,
			'parent_id'		=> null,
			"wallet_id" => $financeWallet->id
		]);
		FinanceOriginModel::create([
			"description" => 'inter Crédito',
			'type_id' 		=> 2,
			'parent_id'		=> $origin->id,
			"wallet_id" => $financeWallet->id
		]);

		$fiannceItem = FinanceItemModel::create([
			"value"     => 1500,
			"date"      => '2023-09-01',
			"sort"      => 1,
			"balance"   => 1,
			"origin_id" => 1,
			"status_id" => 1,
			"type_id"   => 1,
			"wallet_id" => $financeWallet->id
		]);
		$fiannceItem->tags()->sync([1]);
		$fiannceItem->obs()->create(['obs' => 'pagamento', 'item_id' => $fiannceItem->id]);


		$fiannceItem = FinanceItemModel::create([
			"value"     => 500,
			"date"      => '2023-09-015',
			"sort"      => 1,
			"balance"   => 1,
			"origin_id" => 1,
			"status_id" => 1,
			"type_id"   => 1,
			"wallet_id" => $financeWallet->id
		]);
		$fiannceItem->tags()->sync([1]);
		$fiannceItem->obs()->create(['obs' => 'pagamento', 'item_id' => $fiannceItem->id]);


		$fiannceItem = FinanceItemModel::create([
			"value"     => 250,
			"date"      => '2023-09-05',
			"sort"      => 1,
			"balance"   => 2,
			"origin_id" => 1,
			"status_id" => 1,
			"type_id"   => 1,
			"wallet_id" => $financeWallet->id
		]);
		$fiannceItem->tags()->sync([1]);
		$fiannceItem->obs()->create(['obs' => 'pagamento', 'item_id' => $fiannceItem->id]);
	}
}
