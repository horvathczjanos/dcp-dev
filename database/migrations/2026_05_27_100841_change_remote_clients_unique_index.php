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
        Schema::table('badge_connect_remote_clients', function (Blueprint $table) {
            $table->dropForeign(['host_id']);
            $table->dropUnique('badge_connect_remote_clients_host_id_unique');
    
            $table->foreign('host_id')
                ->references('id')
                ->on('badge_connect_hosts')
                ->cascadeOnDelete();
    
            $table->unique(['host_id', 'mode'], 'remote_clients_host_mode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('badge_connect_remote_clients', function (Blueprint $table) {
            $table->dropUnique('remote_clients_host_mode_unique');
    
            $table->dropForeign(['host_id']);
    
            $table->unique('host_id', 'badge_connect_remote_clients_host_id_unique');
    
            $table->foreign('host_id')
                ->references('id')
                ->on('badge_connect_hosts')
                ->cascadeOnDelete();
        });
    }
};
