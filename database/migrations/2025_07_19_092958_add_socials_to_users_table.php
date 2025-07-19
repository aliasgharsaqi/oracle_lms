<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('twitter_profile')->nullable()->after('user_pic');
            $table->string('facebook_profile')->nullable()->after('twitter_profile');
            $table->string('linkedin_profile')->nullable()->after('facebook_profile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['twitter_profile', 'facebook_profile', 'linkedin_profile']);
        });
    }
};
