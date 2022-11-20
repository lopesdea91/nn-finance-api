<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceOriginTypeSeeder extends Seeder
{
	private $nameTable = 'finance_origin_type';

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table($this->nameTable)->insert([
			['id' => 1, 'description' => 'Conta Corrente'],
			['id' => 2, 'description' => 'Cartão de Crédito'],
			['id' => 3, 'description' => 'Cartão VR'],
			['id' => 4, 'description' => 'Cartão Beneficio'],
			['id' => 5, 'description' => 'Cartão VA'],
			['id' => 6, 'description' => 'Conta Poupança'],
			['id' => 7, 'description' => 'Conta Salário'],
			['id' => 8, 'description' => 'Conta Conjunto'],
		]);
	}
}
