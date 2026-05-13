<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_connect_authorization_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('host_id')
                ->constrained('badge_connect_hosts')
                ->cascadeOnDelete();

            $table->string('state', 128)->unique();
            $table->text('code_verifier');
            $table->string('code_challenge', 128);
            $table->text('requested_scope');

            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'host_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_connect_authorization_sessions');
    }
};
