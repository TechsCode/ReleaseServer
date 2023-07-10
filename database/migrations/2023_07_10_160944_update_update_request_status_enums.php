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
        Schema::table('update_requests', function (Blueprint $table) {
            $table->enum('status', ['waiting_authentication', 'unauthorized', 'authorized', 'downloaded'])->default('waiting_authentication')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('update_requests', function (Blueprint $table) {
            $table->enum('status', ['waiting_authentication', 'unauthorized', 'authorized', 'updated'])->default('waiting_authentication')->change();
        });
    }
};
