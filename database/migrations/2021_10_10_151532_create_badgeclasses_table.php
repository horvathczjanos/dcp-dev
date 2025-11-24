<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBadgeclassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badgeclasses', function (Blueprint $table) {
            $table->id();
            $table->string('badgeclassID');
            $table->string('name');
            $table->json('description');
            $table->json('criteria');
            $table->json('tags');
            $table->string('issuerID');
            $table->json('type');
            $table->string('originalLang');
            $table->json('multilangPack');
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
        Schema::dropIfExists('badgeclasses');
    }
}
