<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	private $nameTable = 'finance_wallet_consolidation_balance';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->nameTable, function (Blueprint $table) {
			$table->id();
			$table->double('revenue', 8, 2)->default(0);
			$table->double('expense', 8, 2)->default(0);
			$table->double('available', 8, 2)->default(0);
			$table->double('estimate', 8, 2)->default(0);
			$table->integer('consolidation_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists($this->nameTable);
	}
};
