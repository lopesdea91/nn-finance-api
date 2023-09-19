<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $nameTable = 'finance_list_item';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->nameTable, function (Blueprint $table) {
            $table->id('id');
            $table->double('count', 8, 2);
            $table->double('value', 8, 2);
            $table->string('obs', 300)->default('');
            $table->integer('sort');
            $table->foreignId('type_id')->references('id')->on('finance_type')->onDelete('cascade');
            // $table->foreignId('category_id')->references('id')->on('finance_category');
            // $table->foreignId('group_id')->references('id')->on('finance_group');
            $table->foreignId('list_id')->references('id')->on('finance_list')->onDelete('cascade');
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
