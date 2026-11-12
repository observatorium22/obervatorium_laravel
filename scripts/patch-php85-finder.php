<?php

function observatorium_patch_php85_finder(?string $root = null): void
{
    $root ??= dirname(__DIR__);
    $path = $root.'/vendor/symfony/finder/Iterator/RecursiveDirectoryIterator.php';

    if (! is_file($path)) {
        return;
    }

    $contents = file_get_contents($path);
    $updated = str_replace(
        'public function current(): SplFileInfo',
        'public function current(): \\SplFileInfo|\\FilesystemIterator|string',
        $contents
    );

    if ($updated !== $contents) {
        file_put_contents($path, $updated);
        fwrite(STDOUT, "Patched Symfony Finder for PHP 8.5.\n");
    }
}

if (PHP_SAPI === 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    observatorium_patch_php85_finder();
}
