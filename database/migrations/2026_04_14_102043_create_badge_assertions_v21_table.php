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
        Schema::create('badge_assertions_v21', function (Blueprint $table) {
            $table->id();

            // Helyi felhasználóhoz kötés
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Külső Open Badge assertion azonosító
            $table->string('external_id')->unique();

            // Későbbi bővítéshez opcionális relációk
            $table->unsignedBigInteger('badge_class_id')->nullable();
            $table->unsignedBigInteger('issuer_id')->nullable();

            // Recipient objektum
            $table->json('recipient');

            // Assertion meta
            $table->timestamp('issued_on')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Revocation
            $table->boolean('revoked')->default(false);
            $table->text('revoked_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();

            // Badge kép / baked image / hosted image
            $table->string('image')->nullable();

            // Evidence tömb
            $table->json('evidence')->nullable();

            // Teljes OB 2.1 assertion payload
            $table->json('payload');

            $table->timestamps();

            $table->index('user_id');
            $table->index('issued_on');
            $table->index('revoked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_assertions_v21');
    }
};
