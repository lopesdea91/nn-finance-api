<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $nameTable = 'finance_invoice_item';

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
            $table->string('obs', 300)->default('');
            $table->integer('sort');
            $table->foreignId('type_id')->nullable()->default(null)->references('id')->on('finance_type');
            // $table->foreignId('group_id')->nullable()->default(null)->references('id')->on('finance_group');
            // $table->foreignId('category_id')->nullable()->default(null)->references('id')->on('finance_category');
            $table->foreignId('invoice_id')->references('id')->on('finance_invoice');
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
