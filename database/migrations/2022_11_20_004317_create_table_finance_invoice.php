<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $nameTable = 'finance_invoice';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->nameTable, function (Blueprint $table) {
            $table->id('id');
            $table->double('total', 8, 2);
            $table->date('date');
            $table->enum('enable', [1, 0])->default(1);
            $table->foreignId('origin_id')->nullable()->default(null)->references('id')->on('finance_origin');
            $table->foreignId('status_id')->references('id')->on('finance_status');
            $table->foreignId('wallet_id')->references('id')->on('finance_wallet');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('item_id')->nullable()->references('id')->on('finance_item');
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
