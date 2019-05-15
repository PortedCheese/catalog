<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment("Заголовок поля");
            $table->string('machine')
                ->unique()
                ->comment("Машинное имя поля");
            $table->char('type', 20)
                ->comment('Тип поля в фильтре');
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
        Schema::dropIfExists('category_fields');
    }
}
