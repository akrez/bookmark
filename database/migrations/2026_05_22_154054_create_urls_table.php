<?php

use App\Services\UrlService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2048);
            $table->string('base_url', 2048)->nullable();
            $table->string('title', 512)->nullable();
            $table->string('description', UrlService::DESCRIPTION_LENGTH)->nullable();
            $table->string('favicon', 2048)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
