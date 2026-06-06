<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('check_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('interval')->default(60);
            $table->integer('timeout')->default(10);
            $table->enum('mode', ['auto', 'manual'])->default('manual');
            $table->string('method')->default('both');
            $table->timestamp('starts_at')->nullable();
            $table->boolean('is_running')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('domains_count')->default(0);
            $table->unsignedInteger('checked_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_settings');
    }
};
