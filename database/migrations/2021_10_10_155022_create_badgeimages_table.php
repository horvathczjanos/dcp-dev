<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBadgeimagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badgeimages', function (Blueprint $table) {
            $table->id();
            $table->string('badgeclassID');
            $table->string('filepath');
            $table->string('outputfilename');
            $table->string('imgauthorname');
            $table->string('imgauthoremail');
            $table->string('imgauthorurl');
            $table->string('imgcaption');
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
        Schema::dropIfExists('badgeimages');
    }
}
