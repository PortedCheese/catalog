<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryCategoryFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_category_field', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->integer('category_field_id');
            $table->string('title');
            $table->boolean('filter')
                ->default(0)
                ->comment('Добавить поле в фильтр');
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
        Schema::dropIfExists('category_category_field');
    }
}
