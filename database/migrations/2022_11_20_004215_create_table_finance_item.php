<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	private $nameTable = 'finance_item';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->nameTable, function (Blueprint $table) {
			$table->id('id');
			$table->double('value', 8, 2);
			$table->date('date');
			// $table->string('obs', 300)->default('');
			$table->integer('sort')->nullable()->default(null);
			$table->enum('balance', [1, 0])->default(1);
			// $table->enum('enable', [1, 0])->default(1);
			$table->foreignId('origin_id')->nullable()->default(null)->references('id')->on('finance_origin')->onDelete('cascade');
			$table->foreignId('status_id')->references('id')->on('finance_status');
			$table->foreignId('type_id')->nullable()->default(null)->references('id')->on('finance_type');
			// $table->json('tags_ids')->nullable()->default(null);
			// $table->foreignId('category_id')->nullable()->default(null)->references('id')->on('finance_category');
			// $table->foreignId('group_id')->nullable()->default(null)->references('id')->on('finance_group');
			$table->foreignId('wallet_id')->references('id')->on('finance_wallet')->onDelete('cascade');
			// $table->foreignId('user_id')->references('id')->on('users');
			$table->softDeletes();
			$table->timestamps();
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
