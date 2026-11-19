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
            $table->string('linkedin_url')->nullable()->after('facebook_url');
        });

        SiteSetting::query()->whereNull('linkedin_url')->update([
            'linkedin_url' => SiteSetting::DEFAULT_LINKEDIN_URL,
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('linkedin_url');
        });
    }
};
