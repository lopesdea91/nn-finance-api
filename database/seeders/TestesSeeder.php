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

class TestesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// FinanceItemModel::withTrashed()->first()->delete();
	}
}
