<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $nameTable = 'finance_list';

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
            // $table->enum('enable', [1, 0])->default(1);
            $table->foreignId('origin_id')->nullable()->references('id')->on('finance_origin');
            $table->foreignId('status_id')->nullable()->references('id')->on('finance_status');
            $table->enum('type', ['finance_item', 'finance_invoice'])->default('finance_item');
            $table->integer('type_id')->nullable();
            $table->foreignId('invoice_id')->nullable()->references('id')->on('finance_invoice');
            $table->foreignId('wallet_id')->nullable()->references('id')->on('finance_wallet')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->softDeletes();
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
