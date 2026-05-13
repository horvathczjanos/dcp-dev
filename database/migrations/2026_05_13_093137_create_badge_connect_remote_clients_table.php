<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_connect_remote_clients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('host_id')
                ->constrained('badge_connect_hosts')
                ->cascadeOnDelete();

            $table->string('client_id');
            $table->text('client_secret')->nullable();

            $table->unsignedBigInteger('client_id_issued_at')->nullable();
            $table->unsignedBigInteger('client_secret_expires_at')->nullable();

            $table->string('redirect_uri');
            $table->text('scope');

            $table->json('registration_request')->nullable();
            $table->json('registration_response')->nullable();

            $table->timestamp('registered_at')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamps();

            $table->unique('host_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_connect_remote_clients');
    }
};
