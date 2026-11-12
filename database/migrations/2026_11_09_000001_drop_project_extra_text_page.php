<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = array_values(array_filter(
            ['page_title', 'page_intro', 'page_body', 'page_body2'],
            fn (string $column): bool => Schema::hasColumn('projects', $column),
        ));

        if ($columns === []) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) use ($columns): void {
            $table->dropColumn($columns);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('page_title')->nullable();
            $table->text('page_intro')->nullable();
            $table->longText('page_body')->nullable();
            $table->longText('page_body2')->nullable();
        });
    }
};
