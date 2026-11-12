<?php

use App\Http\Controllers\FrontController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'gallery'])->name('gallery');

Route::get('/projects', [FrontController::class, 'projects'])->name('projects');
Route::get('/projects/{project}/preview', [FrontController::class, 'projects'])->name('projects.preview');
Route::get('/projects/{project}/text', function (Project $project) {
    return redirect()->route('projects.images', $project);
});
Route::get('/projects/{project}', [FrontController::class, 'projectImages'])->name('projects.images');

Route::get('/news', [FrontController::class, 'news'])->name('news');
Route::get('/news/{news}/images', [FrontController::class, 'newsImages'])->name('news.images');
Route::get('/news/{news}', [FrontController::class, 'news'])->name('news.show');

Route::get('/blog', [FrontController::class, 'blog'])->name('blog');
Route::get('/blog/{blog}', [FrontController::class, 'blog'])->name('blog.show');

Route::get('/about', [FrontController::class, 'about'])->name('about');
Route::get('/about/{about}', [FrontController::class, 'about'])->name('about.show');

Route::get('/newsletter', [FrontController::class, 'newsletter'])->name('newsletter');
