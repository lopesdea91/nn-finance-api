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
            $table->string('obs', 300)->default('');
            $table->integer('sort');
            $table->enum('enable', [1, 0])->default(1);
            $table->foreignId('origin_id')->nullable()->default(null)->foreign('origin_id')->references('id')->on('finance_origin');
            $table->foreignId('status_id')->foreign('status_id')->references('id')->on('finance_status');
            $table->foreignId('type_id')->nullable()->default(null)->foreign('type_id')->references('id')->on('finance_type');
            $table->foreignId('category_id')->nullable()->default(null)->foreign('category_id')->references('id')->on('finance_category');
            $table->foreignId('group_id')->nullable()->default(null)->foreign('group_id')->references('id')->on('finance_group');
            $table->foreignId('wallet_id')->foreign('wallet_id')->references('id')->on('finance_wallet');
            $table->foreignId('user_id')->foreign('user_id')->references('id')->on('users');
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
