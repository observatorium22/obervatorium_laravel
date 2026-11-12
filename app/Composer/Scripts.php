<?php

namespace App\Composer;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutput;

class Scripts
{
    private static ?Application $app = null;

    public static function afterAutoloadDump(): void
    {
        self::patchFinder();
        self::writePackageManifest();
    }

    public static function copyEnv(): void
    {
        $env = self::root().'/.env';

        if (! is_file($env)) {
            copy(self::root().'/.env.example', $env);
            fwrite(STDOUT, "Created .env.\n");
        }
    }

    public static function install(?string $name = null, ?string $email = null, ?string $password = null): void
    {
        self::copyEnv();
        self::ensureKey();
        self::ensureDirectories();
        self::patchFinder();

        self::command('package:discover', ['--ansi' => true]);
        self::command('filament:upgrade');
        self::command('vendor:publish', [
            '--tag' => 'laravel-assets',
            '--ansi' => true,
            '--force' => true,
            '--no-interaction' => true,
        ]);
        self::command('migrate', ['--force' => true, '--no-interaction' => true]);
        self::linkPublicStorage();

        if ($name !== null || $email !== null || $password !== null) {
            self::createAdmin($name, $email, $password);
        }
    }

    public static function patchFinder(): void
    {
        require_once self::root().'/scripts/patch-php85-finder.php';

        observatorium_patch_php85_finder(self::root());
    }

    public static function command(string $name, array $parameters = []): void
    {
        $status = self::app()->make(Kernel::class)->call($name, $parameters, new ConsoleOutput);

        if ($status !== 0) {
            throw new RuntimeException($name.' failed.');
        }
    }

    private static function createAdmin(?string $name, ?string $email, ?string $password): void
    {
        if ($name === null || $email === null || $password === null) {
            throw new RuntimeException('Pass --name, --email, and --password together to create an admin user.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('The admin email address must be valid.');
        }

        if (strlen($password) < 8) {
            throw new RuntimeException('The admin password must be at least 8 characters.');
        }

        self::command('make:filament-user', [
            '--name' => $name,
            '--email' => $email,
            '--password' => $password,
            '--panel' => 'admin',
            '--no-interaction' => true,
        ]);
    }

    private static function writePackageManifest(): void
    {
        $root = self::root();
        $installedPath = $root.'/vendor/composer/installed.json';
        $manifest = [];

        if (is_file($installedPath)) {
            $installed = json_decode(file_get_contents($installedPath), true);
            $packages = $installed['packages'] ?? $installed;
            $ignore = [];

            if (is_file($root.'/composer.json')) {
                $ignore = json_decode(file_get_contents($root.'/composer.json'), true)['extra']['laravel']['dont-discover'] ?? [];
            }

            foreach ($packages as $package) {
                $configuration = $package['extra']['laravel'] ?? [];
                $ignore = array_merge($ignore, $configuration['dont-discover'] ?? []);

                if ($configuration !== []) {
                    $manifest[$package['name']] = $configuration;
                }
            }

            $ignoreAll = in_array('*', $ignore, true);

            foreach (array_keys($manifest) as $name) {
                if ($ignoreAll || in_array($name, $ignore, true)) {
                    unset($manifest[$name]);
                }
            }
        }

        $directory = $root.'/bootstrap/cache';

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Could not create bootstrap/cache.');
        }

        file_put_contents(
            $directory.'/packages.php',
            '<?php return '.var_export($manifest, true).';'.PHP_EOL
        );

        fwrite(STDOUT, "Package manifest written.\n");
    }

    private static function ensureKey(): void
    {
        $path = self::root().'/.env';
        $contents = file_get_contents($path);

        if (! preg_match('/^APP_KEY=(.*)$/m', $contents, $matches)) {
            throw new RuntimeException('APP_KEY is missing from .env.');
        }

        if (trim($matches[1]) !== '') {
            return;
        }

        $key = 'base64:'.base64_encode(random_bytes(32));
        $updated = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY='.$key, $contents, 1);

        if ($updated === null) {
            throw new RuntimeException('Could not write APP_KEY.');
        }

        file_put_contents($path, $updated);
        fwrite(STDOUT, "Application key set.\n");
    }

    private static function linkPublicStorage(): void
    {
        $link = self::root().'/public/storage';

        if (file_exists($link)) {
            fwrite(STDOUT, "public/storage is already in place.\n");

            return;
        }

        self::command('storage:link', ['--no-interaction' => true]);
    }

    private static function ensureDirectories(): void
    {
        foreach ([
            'storage/app/public',
            'storage/app/private',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'bootstrap/cache',
        ] as $directory) {
            $path = self::root().'/'.$directory;

            if (! is_dir($path) && ! mkdir($path, 0755, true) && ! is_dir($path)) {
                throw new RuntimeException('Could not create '.$directory.'.');
            }
        }
    }

    private static function app(): Application
    {
        if (self::$app instanceof Application) {
            return self::$app;
        }

        require_once self::root().'/vendor/autoload.php';

        $app = require self::root().'/bootstrap/app.php';

        if (! $app instanceof Application) {
            throw new RuntimeException('Could not bootstrap the application.');
        }

        return self::$app = $app;
    }

    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }
}
