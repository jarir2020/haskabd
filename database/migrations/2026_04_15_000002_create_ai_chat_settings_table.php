<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// AI chat settings -- added 2026-04-15
return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_chat_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_key')->nullable();
            $table->string('provider')->default('openrouter');
            $table->string('model')->default('openrouter/free');
            $table->text('custom_instruction')->nullable();
            $table->integer('max_tokens')->default(300);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_chat_settings');
    }
};
