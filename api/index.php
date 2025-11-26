<?php

// Enable error reporting for debugging (will be caught by try-catch)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Set the correct paths for Vercel
define('LARAVEL_START', microtime(true));

// Wrap everything in try-catch for better error handling
try {
    // Register the Composer autoloader
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Composer autoload file not found. Run: composer install');
    }
    require $autoloadPath;

    // Bootstrap Laravel and handle the request
    $appPath = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($appPath)) {
        throw new Exception('Laravel bootstrap file not found at: ' . $appPath);
    }

    $app = require_once $appPath;

    // Ensure storage directories exist (Vercel serverless limitation workaround)
    $storagePaths = [
        __DIR__ . '/../storage/framework/cache/data',
        __DIR__ . '/../storage/framework/sessions',
        __DIR__ . '/../storage/framework/views',
        __DIR__ . '/../storage/logs',
    ];

    foreach ($storagePaths as $path) {
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (Exception $e) {
    // Log the error
    error_log('Laravel Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());

    // Return a user-friendly error page
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Error</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        h1 {
            font-size: 2.5rem;
            margin: 0 0 20px 0;
        }
        p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin: 10px 0;
        }
        .error-details {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 0.9rem;
            word-break: break-word;
        }
        .hint {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>⚠️ Application Error</h1>
        <p>The application encountered an error and could not complete your request.</p>
        <div class="error-details">
            <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '
        </div>
        <div class="hint">
            <strong>💡 Common fixes:</strong><br>
            • Ensure APP_KEY is set in Vercel environment variables<br>
            • Verify database credentials are correct<br>
            • Check that all required environment variables are configured<br>
            • Review Vercel deployment logs for more details
        </div>
    </div>
</body>
</html>';
    exit(1);
}
