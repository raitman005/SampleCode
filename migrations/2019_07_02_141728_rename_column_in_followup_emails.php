<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnInFollowupEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('followup_emails', function (Blueprint $table) {
            $table->renameColumn('souce_email', 'source_email');
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
            $table->renameColumn('source_email', 'souce_email');
        });
    }
}
