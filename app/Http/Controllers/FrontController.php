<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use App\Models\BlogPost;
use App\Models\NewsItem;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FrontController extends Controller
{
    public function gallery(): View
    {
        $settings = SiteSetting::current();
        $ids = array_map('intval', $settings->featured_project_ids ?? []);
        $projects = Project::published()->with('images')->orderBy('position')->get();

        $featured = collect($ids)
            ->map(fn (int $id) => $projects->firstWhere('legacy_id', $id))
            ->filter();

        $rest = $projects->reject(
            fn (Project $project) => in_array((int) $project->legacy_id, $ids, true)
        )->values();

        return view('gallery', [
            'section' => 'gallery',
            'featured' => $featured,
            'projects' => $rest,
        ]);
    }

    public function projects(?Project $project = null): View
    {
        $projects = Project::published()->with('images')->orderBy('position')->get();
        $selected = $this->selected($projects, $project);

        return view('projects.index', [
            'section' => 'projects',
            'projects' => $projects,
            'selected' => $selected,
        ]);
    }

    public function projectImages(Request $request, Project $project): View
    {
        $this->ensurePublished($project);
        $project->load(['images', 'pdfs']);
        $index = $this->imageIndex($project->images, $request->integer('image'));

        return view('projects.images', [
            'section' => 'projects',
            'project' => $project,
            'index' => $index,
            'image' => $project->images->get($index),
        ]);
    }

    public function news(?NewsItem $news = null): View
    {
        $items = NewsItem::published()->with(['images', 'pdfs'])->orderBy('position')->get();

        return view('news.index', [
            'section' => 'news',
            'items' => $items,
            'selected' => $this->selected($items, $news),
        ]);
    }

    public function newsImages(Request $request, NewsItem $news): View
    {
        return $this->slideshow($request, $news, 'news');
    }

    public function blog(?BlogPost $blog = null): View
    {
        $items = BlogPost::published()->with(['images', 'pdfs'])->orderBy('position')->get();

        return view('blog.index', [
            'section' => 'blog',
            'items' => $items,
            'selected' => $this->selected($items, $blog),
        ]);
    }

    public function about(?AboutPage $about = null): View
    {
        $items = AboutPage::published()->with('pdfs')->orderBy('position')->get();

        return view('about.index', [
            'section' => 'about',
            'items' => $items,
            'selected' => $this->selected($items, $about),
        ]);
    }

    public function newsletter(): View
    {
        return view('newsletter', ['section' => 'about']);
    }

    private function slideshow(Request $request, Model $record, string $section): View
    {
        $this->ensurePublished($record);
        $record->load('images');
        $index = $this->imageIndex($record->images, $request->integer('image'));

        return view('slideshow', [
            'section' => $section,
            'record' => $record,
            'index' => $index,
            'image' => $record->images->get($index),
            'backUrl' => route($section.'.show', $record),
        ]);
    }

    private function selected(Collection $items, ?Model $requested): ?Model
    {
        if ($requested !== null) {
            $this->ensurePublished($requested);

            return $requested;
        }

        return $items->first();
    }

    private function ensurePublished(Model $record): void
    {
        abort_unless((bool) $record->is_published, 404);
    }

    private function imageIndex(Collection $images, int $requested): int
    {
        $count = $images->count();

        if ($count === 0) {
            return 0;
        }

        if ($requested !== 0) {
            $byLegacy = $images->search(
                fn ($image) => (int) $image->legacy_id === $requested
            );

            if ($byLegacy !== false) {
                return (int) $byLegacy;
            }
        }

        if ($requested >= 0 && $requested < $count) {
            return $requested;
        }

        return 0;
    }
}
