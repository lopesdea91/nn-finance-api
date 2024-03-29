<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $nameTable = 'finance_category_closure';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create($this->nameTable, function (Blueprint $table) {
        //     $table->id('id');
        //     $table->enum('closure_type', ['DEPOSIT', 'DRAWAL', 'DRAWAL VARIATION']);
        //     $table->foreignId('category_id')->references('id')->on('finance_category');
        //     $table->foreignId('wallet_id')->references('id')->on('finance_wallet');
        // });
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
