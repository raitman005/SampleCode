<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResendCountInFollowupEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('followup_emails', function (Blueprint $table) {
            $table->integer('resend_count')->default(0)->after('state_id');
            $table->integer('max_resend')->default(0)->after('state_id');
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
            $table->dropColumn('max_resend');
            $table->dropColumn('resend_count');
        });
    }
}
