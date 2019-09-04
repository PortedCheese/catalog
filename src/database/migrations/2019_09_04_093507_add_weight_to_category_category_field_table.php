<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeightToCategoryCategoryFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_category_field', function (Blueprint $table) {
            $table->unsignedInteger("weight")
                ->default(1)
                ->after("id")
                ->comment("Приоритет характеристики для категори");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_category_field', function (Blueprint $table) {
            $table->dropColumn("weight");
        });
    }
}
