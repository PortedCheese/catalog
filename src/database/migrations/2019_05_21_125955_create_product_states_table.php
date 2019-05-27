<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_states', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title')
                ->unique()
                ->comment("Название метки");

            $table->string('slug')
                ->unique()
                ->comment("Машинное имя");

            $table->char('color', 100)
                ->default('secondary')
                ->comment("Цвет метки");

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
        Schema::dropIfExists('product_states');
    }
}
