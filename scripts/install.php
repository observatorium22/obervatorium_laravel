<?php

use App\Composer\Scripts;

$root = dirname(__DIR__);
$autoload = $root.'/vendor/autoload.php';

if (! function_exists('proc_open')) {
    fwrite(STDOUT, "proc_open is disabled. Installing without it.\n");
}

if (! is_file($autoload)) {
    fwrite(STDERR, "vendor/autoload.php is missing.\n");
    fwrite(STDERR, "Copy the vendor directory from a machine where the dependencies are already installed, then run this script again.\n");
    exit(1);
}

require $autoload;

$options = getopt('', ['name:', 'email:', 'password:']);

try {
    Scripts::install(
        $options['name'] ?? null,
        $options['email'] ?? null,
        $options['password'] ?? null,
    );
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}

fwrite(STDOUT, "Installed. Point the web server document root at the public directory.\n");
