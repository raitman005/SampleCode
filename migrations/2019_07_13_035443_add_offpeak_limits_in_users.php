<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOffpeakLimitsInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('initial_offpeak_limit')->after('bank_lead_limit')->default(5);
            $table->integer('bank_offpeak_limit')->after('initial_offpeak_limit')->default(5);
            //
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
            $table->dropColumn('initial_offpeak_limit');
            $table->dropColumn('bank_offpeak_limit');
        });
    }
}
