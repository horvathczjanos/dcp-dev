<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_connect_imported_assertions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('host_id')
                ->constrained('badge_connect_hosts')
                ->cascadeOnDelete();

            $table->string('external_id')->nullable();

            $table->json('assertion_json')->nullable();
            $table->longText('signed_assertion')->nullable();

            $table->timestamp('imported_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'host_id']);
            $table->index('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_connect_imported_assertions');
    }
};
