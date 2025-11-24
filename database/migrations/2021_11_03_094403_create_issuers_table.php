<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issuers', function (Blueprint $table) {
            $table->id();
            $table->string('issuerID');
            $table->string('adminID');
            $table->json('assistantID');
            $table->string('name');
            $table->string('url');
            $table->string('email');
            $table->string('logofile');
            $table->json('customfield');
            $table->json('mailTemplate');
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
        Schema::dropIfExists('issuers');
    }
}
