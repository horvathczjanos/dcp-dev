<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreassertionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preassertions', function (Blueprint $table) {
            $table->id();
            $table->string('recorderID');
            $table->string('modifierID');
            $table->string('issuerID');            
            $table->string('badgeclassID');
            $table->string('recipientType');
            $table->string('originalIdentity');
        //    $table->string('realname');
            $table->string('issuedOn');
            $table->string('neverexpires');
            $table->string('expiresAt');
            $table->json('assertiontype');
            $table->string('released');
            $table->string('status');
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
        Schema::dropIfExists('preassertions');
    }
}
