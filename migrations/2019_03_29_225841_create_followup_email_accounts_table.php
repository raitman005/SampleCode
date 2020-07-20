<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowupEmailAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followup_email_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('host');
            $table->integer('port');
            $table->string('encryption');
            $table->tinyInteger('validate_cert')->default(1);
            $table->string('username');
            $table->string('password', 1024);
            $table->string('protocol')->default('imap');
            $table->string('default_account')->default('default');
            $table->integer('default_max_resend')->default(0);
            $table->unsignedInteger('rank_id');
            $table->timestamps();
            $table->foreign('rank_id')->references('id')->on('ranks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followup_email_accounts');
    }
}
