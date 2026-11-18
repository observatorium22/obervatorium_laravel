<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('instagram_url')->nullable()->after('mission_statement');
            $table->string('facebook_url')->nullable()->after('instagram_url');
        });

        SiteSetting::query()->whereNull('instagram_url')->update([
            'instagram_url' => SiteSetting::DEFAULT_INSTAGRAM_URL,
        ]);

        SiteSetting::query()->whereNull('facebook_url')->update([
            'facebook_url' => SiteSetting::DEFAULT_FACEBOOK_URL,
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn(['instagram_url', 'facebook_url']);
        });
    }
};
