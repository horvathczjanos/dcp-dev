<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarnerBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('earnerbadges', function (Blueprint $table) {
            $table->id();
            $table->string('earnerID');
            $table->string('earnerbadgeID');
            $table->string('earnerbadgepath');
            $table->string('earnerbadgecontenthash');
            $table->longText('cachedBadgeReport');
            $table->string('description');
            $table->string('recipientType');
            $table->string('recipientIdentity');
            $table->string('issearchable');
            $table->longText('earnerBadgeTags');
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
        Schema::dropIfExists('earnerbadges');
    }
}
