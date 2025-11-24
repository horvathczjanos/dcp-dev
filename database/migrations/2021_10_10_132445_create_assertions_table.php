<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssertionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assertions', function (Blueprint $table) {
            $table->id();
            $table->string('assertionID');
            $table->string('badgeclassID');
            $table->string('salt');      
            $table->string('recipientType');
            $table->string('identity');
            $table->string('originalIdentity');
        //    $table->string('realname');
            $table->string('issuedOn');
            $table->string('neverexpires');
            $table->string('expiresAt');
            $table->string('revoked');
            $table->json('revokedReason');
            $table->string('revokedAt');
            $table->string('digitalbadgepath');
            $table->json('assertiontype');
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
        Schema::dropIfExists('assertions');
    }
}
