<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChangeupRankIdInFollowupEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('followup_emails', function (Blueprint $table) {
            $table->unsignedInteger('changeup_rank_id')->default(1)->after('rank_id');
            $table->foreign('changeup_rank_id')->references('id')->on('ranks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('followup_emails', function (Blueprint $table) {
            $table->dropForeign(['changeup_rank_id']);
            $table->dropColumn('changeup_rank_id');
        });
    }
}
