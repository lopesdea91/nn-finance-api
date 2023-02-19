<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	private $nameTable = 'finance_item_obs';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->nameTable, function (Blueprint $table) {
			$table->id();
			$table->string('obs', 300)->default('');
			$table->foreignId('item_id')->references('id')->on('finance_item');
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
