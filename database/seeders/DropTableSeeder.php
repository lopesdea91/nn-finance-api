<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{
	Auth,
	Artisan,
	DB,
	Hash,
	Schema
};

class DropTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Schema::dropIfExists('finance_list_item');
		Schema::dropIfExists('finance_list');
		Schema::dropIfExists('finance_invoice_item');
		Schema::dropIfExists('finance_invoice');
		Schema::dropIfExists('finance_item_tag');
		Schema::dropIfExists('finance_item_repeat');
		Schema::dropIfExists('finance_item_obs');
		Schema::dropIfExists('finance_item');
		Schema::dropIfExists('finance_item');

		Schema::dropIfExists('finance_wallet_consolidation_origin');
		Schema::dropIfExists('finance_origin');
		Schema::dropIfExists('finance_origin_type');


		Schema::dropIfExists('finance_wallet_consolidation_tags');
		Schema::dropIfExists('finance_wallet_consolidation_tag');
		Schema::dropIfExists('finance_wallet_consolidation_month');
		Schema::dropIfExists('finance_wallet_consolidation_balance');
		Schema::dropIfExists('finance_wallet_consolidation_composition');
		Schema::dropIfExists('finance_wallet_composition');

		Schema::dropIfExists('finance_tags');
		Schema::dropIfExists('finance_tag');

		Schema::dropIfExists('finance_status');
		Schema::dropIfExists('finance_type');


		Schema::dropIfExists('finance_wallet');
		Schema::dropIfExists('users');

		Schema::dropIfExists('migrations');
		Schema::dropIfExists('password_resets');
		Schema::dropIfExists('personal_access_tokens');
		Schema::dropIfExists('failed_jobs');
		Schema::dropIfExists('failed_jobs');
	}
}
