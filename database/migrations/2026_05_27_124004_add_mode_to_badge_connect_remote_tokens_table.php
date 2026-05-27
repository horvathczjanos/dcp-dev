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
        Schema::table('badge_connect_remote_tokens', function (Blueprint $table) {
            // A mode oszlop már létezik.
            //$table->string('mode')->default('consumer_read')->after('host_id');
            $table->dropForeign('badge_connect_remote_tokens_user_id_foreign');

            $table->dropUnique('badge_connect_remote_tokens_user_id_host_id_unique');

            $table->foreign('user_id', 'badge_connect_remote_tokens_user_id_foreign')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique(
                ['user_id', 'host_id', 'mode'],
                'remote_tokens_user_host_mode_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('badge_connect_remote_tokens', function (Blueprint $table) {
            $table->dropUnique('remote_tokens_user_host_mode_unique');

            $table->dropForeign('badge_connect_remote_tokens_user_id_foreign');

            $table->unique(
                ['user_id', 'host_id'],
                'badge_connect_remote_tokens_user_id_host_id_unique'
            );

            $table->foreign('user_id', 'badge_connect_remote_tokens_user_id_foreign')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->dropColumn('mode');
        });
    }
};
