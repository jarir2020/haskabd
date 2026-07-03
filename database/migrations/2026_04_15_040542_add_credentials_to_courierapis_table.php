<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Add pathao username/password columns -- added 2026-04-15
return new class extends Migration
{
    public function up()
    {
        Schema::table('courierapis', function (Blueprint $table) {
            if (!Schema::hasColumn('courierapis', 'username')) {
                $table->string('username')->nullable()->after('token');
            }
            if (!Schema::hasColumn('courierapis', 'password')) {
                $table->string('password')->nullable()->after('username');
            }
        });
    }

    public function down()
    {
        Schema::table('courierapis', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }
};
