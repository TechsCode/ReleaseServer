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
        Schema::create('update_requests', function (Blueprint $table) {
            $table->id();
            $table->string('update_token')->unique();
            $table->enum('status', ['waiting_authentication', 'unauthorized', 'authorized', 'updated'])->default('waiting_authentication');
            $table->string('plugin_name')->nullable();
            $table->string('current_version')->nullable();
            $table->string('update_to')->nullable();
            $table->string('allowed_plugins')->nullable();
            $table->boolean('has_beta_access')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_requests');
    }
};
