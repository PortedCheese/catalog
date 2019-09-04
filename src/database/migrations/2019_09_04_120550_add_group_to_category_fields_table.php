<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupToCategoryFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_fields', function (Blueprint $table) {
            $table->integer("group_id")
                ->nullable()
                ->after("machine")
                ->comment("Группа поля");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_fields', function (Blueprint $table) {
            $table->dropColumn("group_id");
        });
    }
}
