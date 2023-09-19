<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceStatusSeeder extends Seeder
{
  private $nameTable = 'finance_status';

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table($this->nameTable)->insert([
      ['id' => 1, 'description' => 'Ok'],
      ['id' => 2, 'description' => 'Pendente'],
    ]);
  }
}
