<?php

declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: application/json');

$provided = (string) ($_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_POST['token'] ?? '');
$expected = (string) env('DEPLOY_TOKEN', '');

if ($expected === '' || !hash_equals($expected, $provided)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Forbidden']);
    exit;
}

use Illuminate\Support\Facades\Artisan;

$commands = [
    ['migrate', ['--force' => true]],
    ['config:clear', []],
    ['config:cache', []],
    ['route:cache', []],
    ['view:cache', []],
    ['storage:link', []],
];

$results = [];

foreach ($commands as [$command, $arguments]) {
    try {
        $exitCode = Artisan::call($command, $arguments);
        $output = trim(Artisan::output());

        if ($command === 'storage:link' && $exitCode !== 0 && stripos($output, 'already exists') !== false) {
            $exitCode = 0;
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
        }

        $results[$command] = [
            'exit_code' => $exitCode,
            'output' => $message,
        ];
    }
}

$failed = array_filter($results, static fn (array $result): bool => ($result['exit_code'] ?? 1) !== 0);

http_response_code(empty($failed) ? 200 : 500);
echo json_encode([
    'ok' => empty($failed),
    'results' => $results,
], JSON_PRETTY_PRINT);
