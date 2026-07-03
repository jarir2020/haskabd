<?php

declare(strict_types=1);

set_time_limit(600);
ini_set('max_execution_time', '600');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed']);
}

$hookDir = __DIR__;
$env = loadEnvFile($hookDir . '/.env');
$basePath = resolveDeployBasePath($hookDir, $env);

if (is_file($basePath . '/.env')) {
    $env = array_merge($env, loadEnvFile($basePath . '/.env'));
}

$provided = (string) ($_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_POST['token'] ?? '');
$expected = (string) ($env['DEPLOY_TOKEN'] ?? '');

if ($expected === '' || !hash_equals($expected, $provided)) {
    respond(403, ['ok' => false, 'error' => 'Forbidden']);
}

$results = ['deploy_path' => $basePath];
$preservePaths = ['.env', 'public/uploads'];

try {
    $backupDir = backupPreservedPaths($basePath, $preservePaths);

    if (isGitAvailable() && canUseGit($basePath)) {
        $results['source'] = pullWithGit($basePath, $env);
    } else {
        $results['source'] = pullFromGithubArchive($basePath, $env);
    }

    restorePreservedPaths($basePath, $backupDir, $preservePaths);
    removeDirectory($backupDir);

    $results['composer'] = runComposerInstall($basePath);
    $results['artisan'] = runArtisanTasks($basePath);

    $failed = collectFailures($results);
    respond(empty($failed) ? 200 : 500, [
        'ok' => empty($failed),
        'results' => $results,
    ]);
} catch (Throwable $exception) {
    respond(500, [
        'ok' => false,
        'error' => $exception->getMessage(),
        'results' => $results,
    ]);
}

function respond(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_PRETTY_PRINT);
    exit;
}

function loadEnvFile(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $vars = [];

    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $vars[trim($key)] = trim($value, " \t\"'");
    }

    return $vars;
}

function resolveDeployBasePath(string $hookDir, array $env): string
{
    $configured = trim($env['DEPLOY_BASE_PATH'] ?? '');

    if ($configured === '') {
        return isValidLaravelRoot($hookDir) ? $hookDir : throw new RuntimeException(
            'deploy-hook.php is not inside a Laravel app. Remove DEPLOY_BASE_PATH from .env or move the hook into the app root.'
        );
    }

    if (str_starts_with($configured, '/')) {
        $candidate = rtrim(str_replace('\\', '/', $configured), '/');
    } else {
        $resolved = realpath($hookDir . DIRECTORY_SEPARATOR . $configured);
        if ($resolved === false) {
            throw new RuntimeException('DEPLOY_BASE_PATH does not exist: ' . $configured);
        }
        $candidate = $resolved;
    }

    if (!isValidLaravelRoot($candidate)) {
        throw new RuntimeException('DEPLOY_BASE_PATH is not a Laravel app root: ' . $candidate);
    }

    return $candidate;
}

function isValidLaravelRoot(string $path): bool
{
    return is_file($path . '/composer.json') && is_file($path . '/bootstrap/app.php');
}

function isGitAvailable(): bool
{
    return function_exists('exec') && runCommand('git --version', $output, $code) && $code === 0;
}

function canUseGit(string $basePath): bool
{
    return is_dir($basePath . '/.git') || is_writable($basePath);
}

function pullWithGit(string $basePath, array $env): array
{
    $branch = $env['DEPLOY_GIT_BRANCH'] ?? 'main';
    $repo = $env['DEPLOY_GIT_REPO'] ?? 'https://github.com/jarir2020/haskabd.git';
    $token = $env['DEPLOY_GIT_TOKEN'] ?? '';

    if ($token !== '') {
        $repo = preg_replace('#^https://#', 'https://' . rawurlencode($token) . '@', $repo) ?? $repo;
    }

    $escapedBase = escapeshellarg($basePath);
    $escapedRepo = escapeshellarg($repo);

    if (!is_dir($basePath . '/.git')) {
        runCommand("git -C {$escapedBase} init", $output, $code, true);
        runCommand("git -C {$escapedBase} remote add origin {$escapedRepo}", $output, $code, true);
    } else {
        runCommand("git -C {$escapedBase} remote set-url origin {$escapedRepo}", $output, $code, true);
    }

    runCommand("git -C {$escapedBase} fetch --depth 1 origin {$branch}", $output, $code, true);
    runCommand("git -C {$escapedBase} reset --hard FETCH_HEAD", $output, $code, true);

    return [
        'method' => 'git',
        'exit_code' => 0,
        'output' => trim($output ?? ''),
    ];
}

function pullFromGithubArchive(string $basePath, array $env): array
{
    $owner = $env['DEPLOY_GITHUB_OWNER'] ?? 'jarir2020';
    $repo = $env['DEPLOY_GITHUB_REPO'] ?? 'haskabd';
    $branch = $env['DEPLOY_GIT_BRANCH'] ?? 'main';
    $token = $env['DEPLOY_GIT_TOKEN'] ?? '';

    $url = "https://api.github.com/repos/{$owner}/{$repo}/zipball/{$branch}";
    $zipPath = sys_get_temp_dir() . '/deploy-' . uniqid('', true) . '.zip';
    $extractPath = sys_get_temp_dir() . '/deploy-' . uniqid('', true);

    mkdir($extractPath, 0755, true);

    $headers = ['User-Agent: haskabd-deploy-hook'];
    if ($token !== '') {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    downloadFile($url, $zipPath, $headers);

    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        throw new RuntimeException('Unable to open downloaded deploy archive.');
    }

    $zip->extractTo($extractPath);
    $zip->close();
    unlink($zipPath);

    $roots = array_values(array_filter(glob($extractPath . '/*') ?: [], 'is_dir'));
    if ($roots === []) {
        throw new RuntimeException('Downloaded archive did not contain a source directory.');
    }

    syncDirectory($roots[0], $basePath, preservedRelativePaths());
    removeDirectory($extractPath);

    return [
        'method' => 'github-archive',
        'exit_code' => 0,
        'output' => 'Archive extracted and synced.',
    ];
}

function preservedRelativePaths(): array
{
    return [
        '.env',
        'public/uploads',
        'storage/logs',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
    ];
}

function shouldPreserve(string $relativePath, array $preserve): bool
{
    $relativePath = str_replace('\\', '/', $relativePath);

    foreach ($preserve as $prefix) {
        if ($relativePath === $prefix || str_starts_with($relativePath, $prefix . '/')) {
            return true;
        }
    }

    return false;
}

function syncDirectory(string $source, string $destination, array $preserve): void
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        /** @var SplFileInfo $item */
        $relative = substr(str_replace('\\', '/', $item->getPathname()), strlen(str_replace('\\', '/', $source)) + 1);

        if (shouldPreserve($relative, $preserve)) {
            continue;
        }

        $target = $destination . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);

        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
            continue;
        }

        $parent = dirname($target);
        if (!is_dir($parent)) {
            mkdir($parent, 0755, true);
        }

        copy($item->getPathname(), $target);
    }
}

function backupPreservedPaths(string $basePath, array $paths): string
{
    $backupDir = sys_get_temp_dir() . '/deploy-backup-' . uniqid('', true);
    mkdir($backupDir, 0755, true);

    foreach ($paths as $relative) {
        $source = $basePath . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $target = $backupDir . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);

        if (is_file($source)) {
            $parent = dirname($target);
            if (!is_dir($parent)) {
                mkdir($parent, 0755, true);
            }
            copy($source, $target);
        } elseif (is_dir($source)) {
            copyDirectory($source, $target);
        }
    }

    return $backupDir;
}

function restorePreservedPaths(string $basePath, string $backupDir, array $paths): void
{
    if (!is_dir($backupDir)) {
        return;
    }

    foreach ($paths as $relative) {
        $source = $backupDir . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $target = $basePath . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);

        if (is_file($source)) {
            $parent = dirname($target);
            if (!is_dir($parent)) {
                mkdir($parent, 0755, true);
            }
            copy($source, $target);
        } elseif (is_dir($source)) {
            copyDirectory($source, $target);
        }
    }
}

function copyDirectory(string $source, string $destination): void
{
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        /** @var SplFileInfo $item */
        $relative = substr($item->getPathname(), strlen($source) + 1);
        $target = $destination . DIRECTORY_SEPARATOR . $relative;

        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
        } else {
            $parent = dirname($target);
            if (!is_dir($parent)) {
                mkdir($parent, 0755, true);
            }
            copy($item->getPathname(), $target);
        }
    }
}

function removeDirectory(string $path): void
{
    if (!is_dir($path)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }

    rmdir($path);
}

function downloadFile(string $url, string $destination, array $headers = []): void
{
    if (function_exists('curl_init')) {
        $handle = curl_init($url);
        $file = fopen($destination, 'wb');

        curl_setopt_array($handle, [
            CURLOPT_FILE => $file,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 300,
        ]);

        $ok = curl_exec($handle);
        $error = curl_error($handle);
        curl_close($handle);
        fclose($file);

        if (!$ok) {
            throw new RuntimeException('Download failed: ' . $error);
        }

        return;
    }

    $headerLines = implode("\r\n", $headers);
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => $headerLines,
            'timeout' => 300,
        ],
    ]);

    $contents = file_get_contents($url, false, $context);
    if ($contents === false) {
        throw new RuntimeException('Download failed using file_get_contents.');
    }

    file_put_contents($destination, $contents);
}

function runComposerInstall(string $basePath): array
{
    $escapedBase = escapeshellarg($basePath);
    $commands = [
        "cd {$escapedBase} && composer install --no-dev --optimize-autoloader --no-interaction 2>&1",
        "cd {$escapedBase} && /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1",
        "cd {$escapedBase} && /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1",
        "cd {$escapedBase} && /usr/local/bin/ea-php83 /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1",
        "cd {$escapedBase} && php composer.phar install --no-dev --optimize-autoloader --no-interaction 2>&1",
    ];

    $lastOutput = '';

    foreach ($commands as $command) {
        if (runCommand($command, $output, $code) && $code === 0) {
            return [
                'exit_code' => $code,
                'output' => trim($output),
            ];
        }

        $lastOutput = trim($output);
    }

    if (is_file($basePath . '/vendor/autoload.php')) {
        return [
            'exit_code' => 0,
            'output' => 'Composer command failed, but vendor/autoload.php is available.',
        ];
    }

    return [
        'exit_code' => 1,
        'output' => $lastOutput !== '' ? $lastOutput : 'Composer command could not be executed on the server.',
    ];
}

function runArtisanTasks(string $basePath): array
{
    define('LARAVEL_START', microtime(true));

    require $basePath . '/vendor/autoload.php';
    $app = require $basePath . '/bootstrap/app.php';

    /** @var \Illuminate\Contracts\Console\Kernel $kernel */
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $commands = [
        ['migrate', ['--force' => true], false],
        ['config:clear', [], false],
        ['config:cache', [], false],
        ['route:cache', [], true],
        ['view:cache', [], true],
        ['storage:link', [], true],
    ];

    $results = [];

    foreach ($commands as [$command, $arguments, $optional]) {
        try {
            $exitCode = Illuminate\Support\Facades\Artisan::call($command, $arguments);
            $output = trim(Illuminate\Support\Facades\Artisan::output());

            if ($command === 'storage:link' && $exitCode !== 0 && stripos($output, 'already exists') !== false) {
                $exitCode = 0;
            }

            if ($optional && $exitCode !== 0) {
                if ($command === 'route:cache') {
                    Illuminate\Support\Facades\Artisan::call('route:clear');
                }

                $exitCode = 0;
                $output = 'skipped: ' . ($output !== '' ? $output : $command . ' failed');
            }

            $results[$command] = [
                'exit_code' => $exitCode,
                'output' => $output,
            ];
        } catch (Throwable $exception) {
            $message = $exception->getMessage();
            $exitCode = 1;

            if ($command === 'storage:link' && stripos($message, 'already exists') !== false) {
                $exitCode = 0;
                $message = 'link already exists';
            } elseif ($optional) {
                if ($command === 'route:cache') {
                    try {
                        Illuminate\Support\Facades\Artisan::call('route:clear');
                    } catch (Throwable) {
                    }
                }

                $exitCode = 0;
                $message = 'skipped: ' . $message;
            }

            $results[$command] = [
                'exit_code' => $exitCode,
                'output' => $message,
            ];
        }
    }

    return $results;
}

function collectFailures(array $results): array
{
    $failed = [];

    foreach ($results as $section => $sectionResult) {
        if ($section === 'artisan' && is_array($sectionResult)) {
            foreach ($sectionResult as $command => $result) {
                if (($result['exit_code'] ?? 1) !== 0) {
                    $failed[] = $command;
                }
            }
            continue;
        }

        if (($sectionResult['exit_code'] ?? 0) !== 0) {
            $failed[] = $section;
        }
    }

    return $failed;
}

function runCommand(string $command, ?string &$output, ?int &$exitCode, bool $strict = false): bool
{
    if (!function_exists('exec')) {
        if ($strict) {
            throw new RuntimeException('PHP exec() is disabled on this server.');
        }

        $output = 'exec() is disabled on this server.';
        $exitCode = 1;
        return false;
    }

    $lines = [];
    exec($command, $lines, $exitCode);
    $output = implode("\n", $lines);

    if ($strict && $exitCode !== 0) {
        throw new RuntimeException(trim($output) !== '' ? $output : "Command failed: {$command}");
    }

    return true;
}
