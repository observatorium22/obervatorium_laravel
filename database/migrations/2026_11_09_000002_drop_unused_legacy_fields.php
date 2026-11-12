<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->drop('site_settings', ['home_caption', 'redirect_legacy_id']);
        $this->drop('projects', ['text_image_path']);
        $this->drop('news_items', ['intro', 'body2']);
        $this->drop('blog_posts', ['intro', 'body2']);
        $this->drop('about_pages', ['subtitle', 'intro', 'body2']);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->text('home_caption')->nullable();
            $table->unsignedBigInteger('redirect_legacy_id')->nullable();
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->string('text_image_path')->nullable();
        });

        foreach (['news_items', 'blog_posts', 'about_pages'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->text('intro')->nullable();
                $table->longText('body2')->nullable();
            });
        }

        Schema::table('about_pages', function (Blueprint $table): void {
            $table->string('subtitle')->nullable();
        });
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function drop(string $table, array $columns): void
    {
        $present = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($table, $column),
        ));

        if ($present === []) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($present): void {
            $table->dropColumn($present);
        });
    }
};
