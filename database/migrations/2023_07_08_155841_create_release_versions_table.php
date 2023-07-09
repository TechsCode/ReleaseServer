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
        Schema::create('release_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('build_id');
            $table->string('plugin_name');
            $table->string('plugin_version');
            $table->timestamps();

            $table->foreign('build_id')->references('id')->on('builds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_versions');
    }
};
