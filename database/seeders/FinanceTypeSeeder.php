<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceTypeSeeder extends Seeder
{
	private $nameTable = 'finance_type';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table($this->nameTable)->insert([
			['id' => 1, 'description' => 'Receita'],
			['id' => 2, 'description' => 'Despesa'],
		]);
	}
}
