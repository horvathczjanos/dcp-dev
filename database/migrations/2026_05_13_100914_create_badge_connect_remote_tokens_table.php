<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_connect_remote_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('host_id')
                ->constrained('badge_connect_hosts')
                ->cascadeOnDelete();

            $table->text('access_token');
            $table->text('refresh_token')->nullable();

            $table->string('token_type')->nullable();
            $table->integer('expires_in')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->text('scope')->nullable();
            $table->json('token_response')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'host_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_connect_remote_tokens');
    }
};
