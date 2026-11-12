<?php

$path = dirname(__DIR__).'/vendor/symfony/finder/Iterator/RecursiveDirectoryIterator.php';

if (! is_file($path)) {
    exit(0);
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
