<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issuer_metabadge_rules', function (Blueprint $table) {
            $table->id();
            $table->string('issuerID');
            $table->string('userID');
            $table->json('rulecontent');
            $table->json('description')->nullable();
            $table->json('log')->nullable();
            $table->string('enabled');
            $table->string('deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuer_metabadge_rules');
    }
};
