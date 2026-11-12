<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$root = dirname(__DIR__);

foreach ([
    $root.'/storage/app/private',
    $root.'/storage/app/public',
    $root.'/storage/framework/cache/data',
    $root.'/storage/framework/sessions',
    $root.'/storage/framework/views',
    $root.'/storage/logs',
    $root.'/bootstrap/cache',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

return Application::configure(basePath: $root)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
