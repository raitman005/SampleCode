<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_lead')->default(0);
            $table->tinyInteger('approved')->default(0);
            $table->unsignedInteger('under_the_lead_id')->nullable();
            $table->foreign('under_the_lead_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['under_the_lead_id']);
            $table->dropColumn('is_lead');
            $table->dropColumn('approved');
        });
    }
}
