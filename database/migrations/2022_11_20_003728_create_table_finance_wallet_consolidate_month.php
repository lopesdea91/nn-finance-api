<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $nameTable = 'finance_wallet_consolidate_month';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->nameTable, function (Blueprint $table) {
            $table->id('id');
            $table->integer('year');
            $table->integer('month');
            $table->json('balance');
            $table->json('group');
            $table->json('category');
            $table->json('origin');
            $table->json('invoice');
            $table->foreignId('wallet_id')->foreign('wallet_id')->references('id')->on('finance_wallet')->onDelete('cascade');
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
