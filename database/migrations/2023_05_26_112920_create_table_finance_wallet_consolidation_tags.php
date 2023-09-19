<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	private $nameTable = 'finance_wallet_consolidation_tags';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->nameTable, function (Blueprint $table) {
			$table->id();
			$table->foreignId('consolidation_tag_id')->references('id')->on('finance_wallet_consolidation_tag');
			$table->foreignId('tag_id')->references('id')->on('finance_tag')->onDelete('cascade');
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
