<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    
    public function up(): void
    {
        Schema::create('badge_connect_hosts', function (Blueprint $table) {
            $table->id();

            // Emberi név a DCP admin/user felületen
            $table->string('name');

            // Pl.: https://certification.imsglobal.org/badgeconnect
            $table->string('base_url')->unique();

            // Pl.: https://certification.imsglobal.org/badgeconnect/.well-known/badgeconnect.json
            $table->string('manifest_url')->unique();

            // A manifestből jönnek
            $table->string('api_base')->nullable();
            $table->string('registration_url')->nullable();
            $table->string('authorization_url')->nullable();
            $table->string('token_url')->nullable();
            $table->string('token_revocation_url')->nullable();

            // A manifestben meghirdetett scope-ok
            $table->json('scopes_offered')->nullable();

            // Teljes manifest nyersen, hibakereséshez és újrafeldolgozáshoz
            $table->json('manifest_json')->nullable();

            // Állapotkezelés
            $table->boolean('is_active')->default(true);
            $table->timestamp('manifest_fetched_at')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_connect_hosts');
    }
};
