<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mission_statement')->nullable();
            $table->json('featured_project_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $this->identity($table);
            $table->string('location')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $this->copy($table);
            $table->boolean('is_featured')->default(false);
            $this->publishing($table);
        });

        Schema::create('news_items', function (Blueprint $table) {
            $table->id();
            $this->identity($table);
            $this->dated($table);
            $this->copy($table, withColumns: false);
            $this->publishing($table);
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $this->identity($table);
            $this->dated($table);
            $this->copy($table, withColumns: false);
            $this->publishing($table);
        });

        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $this->identity($table, withSubtitle: false);
            $this->copy($table, withColumns: false);
            $this->publishing($table);
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->string('kind');
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('path')->nullable();
            $table->string('display_path')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('about_pages');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('news_items');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('site_settings');
    }

    private function identity(Blueprint $table, bool $withSubtitle = true): void
    {
        $table->unsignedBigInteger('legacy_id')->nullable()->unique();
        $table->string('title')->nullable();

        if ($withSubtitle) {
            $table->string('subtitle')->nullable();
        }
    }

    private function copy(Blueprint $table, bool $withColumns = true): void
    {
        if ($withColumns) {
            $table->text('intro')->nullable();
        }

        $table->longText('body')->nullable();

        if ($withColumns) {
            $table->longText('body2')->nullable();
        }

        $table->string('image_path')->nullable();
    }

    private function dated(Blueprint $table): void
    {
        $table->string('date_label')->nullable();
        $table->date('published_on')->nullable();
        $table->unsignedSmallInteger('year')->nullable();
    }

    private function publishing(Blueprint $table): void
    {
        $table->unsignedInteger('position')->default(0);
        $table->boolean('is_published')->default(true);
        $table->timestamps();
    }
};
