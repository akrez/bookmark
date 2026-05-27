<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2048);
            $table->string('base_url', 512)->nullable();
            $table->string('collection', 512)->nullable();
            $table->string('title', 512)->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('shared_at')->nullable();
            $table->timestamp('favorited_at')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
