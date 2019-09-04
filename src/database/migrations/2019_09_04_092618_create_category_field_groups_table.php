<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryFieldGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_field_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string("title")
                ->comment("Заголовок группы");

            $table->string("machine")
                ->unique()
                ->comment("Машинное имя группы");

            $table->unsignedInteger("weight")
                ->default(1)
                ->comment("Приоритет группы");

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
        Schema::dropIfExists('category_field_groups');
    }
}
