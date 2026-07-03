<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('otp'); // hashed
            $table->boolean('verified')->default(false);
            $table->timestamp('expires_at');       // OTP valid for 1 min
            $table->timestamp('next_request_at');  // new OTP allowed after 5 min
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_reset_otps');
    }
};
