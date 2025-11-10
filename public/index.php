<?php

// public/index.php

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

// Bootstrap the app
$app = require_once __DIR__.'/../bootstrap/app.php';

// Load .env.installer if .env does not exist
$envPath = __DIR__.'/../.env';
if (!file_exists($envPath) && file_exists(__DIR__.'/../.env.installer')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../', '.env.installer');
    $dotenv->safeLoad();
}

// Run the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
