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
        Schema::create('badge_connect_exported_assertions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('host_id')
                ->constrained('badge_connect_hosts')
                ->cascadeOnDelete();

            $table->foreignId('assertion_id')
                ->constrained('badge_assertions_v21')
                ->cascadeOnDelete();

            $table->string('mode')->default('consumer_write');

            $table->json('request_json')->nullable();
            $table->json('response_json')->nullable();

            $table->integer('http_status')->nullable();
            $table->boolean('success')->default(false);
            $table->text('last_error')->nullable();

            $table->timestamp('exported_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'host_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_connect_exported_assertions');
    }
};
