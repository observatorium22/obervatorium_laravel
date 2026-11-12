<?php

namespace App\Console\Commands;

use App\Models\AboutPage;
use App\Models\BlogPost;
use App\Models\NewsItem;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ImportObservatorium extends Command
{
    protected $signature = 'observatorium:import {path? : Path to data.json}';

    protected $description = 'Import projects, news, blog, and about pages from the existing site export';

    public function handle(): int
    {
        $path = $this->argument('path') ?: config('observatorium.data_json');

        if (! is_file($path)) {
            $this->error('Export not found: '.$path);

            return self::FAILURE;
        }

        $decoded = json_decode(file_get_contents($path), true);
        $root = $decoded[0] ?? $decoded;

        if (! is_array($root)) {
            $this->error('The export is not a JSON object.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($root) {
            $this->importProjects($root['project'] ?? []);
            $this->importDated(NewsItem::class, $root['news'] ?? [], 'news');
            $this->importDated(BlogPost::class, $root['blog'] ?? [], 'blog posts');
            $this->importDated(AboutPage::class, $root['about'] ?? [], 'about pages', withDate: false, withSubtitle: false);
            $this->importSettings($root['algemeen'][0] ?? []);
        });

        $this->info('Import finished.');

        return self::SUCCESS;
    }

    private function importSettings(array $row): void
    {
        $ids = array_values(array_filter(array_map(
            fn (string $id) => (int) trim($id),
            explode(',', (string) ($row['selected_projects_grid'] ?? ''))
        )));

        SiteSetting::query()->updateOrCreate(['id' => SiteSetting::query()->value('id') ?? 1], [
            'mission_statement' => $this->blank($row['mission_statement'] ?? null),
            'featured_project_ids' => $ids,
        ]);

        Project::query()->update(['is_featured' => false]);

        if ($ids !== []) {
            Project::query()->whereIn('legacy_id', $ids)->update(['is_featured' => true]);
        }

        $this->line('Site settings imported.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function importProjects(array $rows): void
    {
        foreach ($rows as $position => $row) {
            $project = Project::query()->updateOrCreate(
                ['legacy_id' => (int) $row['obj_id']],
                [
                    'title' => $this->blank($row['title'] ?? null),
                    'subtitle' => $this->blank($row['subtitle'] ?? null),
                    'location' => $this->blank($row['location'] ?? null),
                    'year' => $this->nullableInt($row['year'] ?? null),
                    'intro' => $this->blank($row['intro'] ?? null),
                    'body' => $this->blank($row['body'] ?? null),
                    'body2' => $this->blank($row['body2'] ?? null),
                    'image_path' => $this->blank($row['image_source'] ?? null),
                    'position' => $position,
                    'is_published' => true,
                ]
            );

            $this->syncMedia($project, $row);
        }

        $this->line(count($rows).' projects imported.');
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function importDated(string $model, array $rows, string $label, bool $withDate = true, bool $withSubtitle = true): void
    {
        foreach ($rows as $position => $row) {
            $attributes = [
                'title' => $this->blank($row['title'] ?? null),
                'body' => $this->blank($row['body'] ?? null),
                'image_path' => $this->blank($row['image_source'] ?? null),
                'position' => $position,
                'is_published' => true,
            ];

            if ($withSubtitle) {
                $attributes['subtitle'] = $this->blank($row['subtitle'] ?? null);
            }

            if ($withDate) {
                $attributes['date_label'] = $this->blank($row['date'] ?? null);
                $attributes['year'] = $this->nullableInt($row['year'] ?? null);
                $attributes['published_on'] = $this->parseDate($row['date'] ?? null);
            }

            $record = $model::query()->updateOrCreate(
                ['legacy_id' => (int) $row['obj_id']],
                $attributes
            );

            $this->syncMedia($record, $row);
        }

        $this->line(count($rows).' '.$label.' imported.');
    }

    private function syncMedia(Model $record, array $row): void
    {
        $record->media()->delete();

        foreach ($row['media'] ?? [] as $position => $media) {
            $path = $this->blank($media['uploaded_file'] ?? null);
            $display = $this->blank($media['image_source_m'] ?? null);

            if ($path === null && $display === null) {
                continue;
            }

            $record->media()->create([
                'legacy_id' => $this->nullableInt($media['obj_id'] ?? null),
                'kind' => 'image',
                'title' => $this->blank($media['title'] ?? null),
                'text' => $this->blank($media['text'] ?? null),
                'path' => $path ?: $display,
                'display_path' => $display,
                'width' => $this->nullableInt($media['width'] ?? null),
                'height' => $this->nullableInt($media['height'] ?? null),
                'position' => $position,
            ]);
        }

        foreach ($row['pdf'] ?? [] as $position => $pdf) {
            $path = $this->blank($pdf['pdf_source'] ?? null);

            if ($path === null) {
                continue;
            }

            $record->media()->create([
                'legacy_id' => $this->nullableInt($pdf['obj_id'] ?? null),
                'kind' => 'pdf',
                'title' => $this->blank($pdf['title'] ?? null) ?: $this->blank($pdf['caption'] ?? null),
                'text' => $this->blank($pdf['caption'] ?? null),
                'path' => $path,
                'position' => $position,
            ]);
        }
    }

    private function blank(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function parseDate(mixed $value): ?string
    {
        $value = $this->blank(is_string($value) ? $value : null);

        if ($value === null) {
            return null;
        }

        $date = \DateTime::createFromFormat('!d/m/Y', $value);

        return $date instanceof \DateTime ? $date->format('Y-m-d') : null;
    }
}
