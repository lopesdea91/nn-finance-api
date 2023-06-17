<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	private $nameTable = 'finance_wallet_consolidation_origin';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->nameTable, function (Blueprint $table) {
			$table->id();
			$table->double('sum', 8, 2);
			$table->double('revenue', 8, 2)->nullable();
			$table->double('expense', 8, 2)->nullable();
			$table->double('average', 8, 2)->nullable();
			$table->foreignId('origin_id')->references('id')->on('finance_origin');
			$table->integer('consolidation_id')->references('id')->on('finance_wallet_consolidation_month');
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
