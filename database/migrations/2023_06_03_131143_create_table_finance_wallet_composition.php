<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	private $nameTable = 'finance_wallet_composition';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->nameTable, function (Blueprint $table) {
			$table->id();
			$table->double('percentage_limit', 8, 2)->default(0);
			$table->foreignId('tag_id')->references('id')->on('finance_tag')->onDelete('cascade');
			$table->foreignId('wallet_id')->references('id')->on('finance_wallet')->onDelete('cascade');
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
