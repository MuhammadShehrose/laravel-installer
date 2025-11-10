<?php

namespace Installer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class InstallerController extends Controller
{
    /**
     * Step 1: Show welcome screen with "Start Installation" button.
     */
    public function welcome()
    {
        $envPath = base_path('.env');
        $envExample = base_path('.env.installer');

        if (!file_exists($envPath) && is_writable(base_path())) {
            copy($envExample, $envPath);
        }

        return view('installer::install.welcome');
    }

    /**
     * Step 2: Run system requirement checks.
     */
    public function check()
    {
        // PHP version check
        $requirements['php'] = version_compare(PHP_VERSION, '8.2.0', '>=');

        // Required PHP extensions
        $requiredExtensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json'];
        $requirements['extensions'] = [];
        foreach ($requiredExtensions as $ext) {
            $requirements['extensions'][$ext] = extension_loaded($ext);
        }

        // Writable paths
        $paths = [
            'storage' => is_writable(storage_path()),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env' => is_writable(base_path('.env')),
        ];
        $requirements['permissions'] = $paths;

        // Determine if all requirements passed
        $allPassed = $requirements['php'];
        foreach ($requirements['extensions'] as $ok) {
            $allPassed = $allPassed && $ok;
        }
        foreach ($requirements['permissions'] as $ok) {
            $allPassed = $allPassed && $ok;
        }

        return view('installer::install.check', compact('requirements', 'allPassed'));
    }

    /**
     * Step 3: Database configuration form and connection test.
     */
    public function database(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('installer::install.database');
        }

        // Validate input
        $data = $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            // 1️⃣ Create temporary connection without database to create it
            config([
                'database.connections.temp' => [
                    'driver' => 'mysql',
                    'host' => $data['db_host'],
                    'port' => $data['db_port'],
                    'database' => null,
                    'username' => $data['db_user'],
                    'password' => $data['db_password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
            ]);

            $pdo = DB::connection('temp')->getPdo();
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$data['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2️⃣ Update .env
            $this->updateEnv([
                'DB_HOST' => $data['db_host'],
                'DB_PORT' => $data['db_port'],
                'DB_DATABASE' => $data['db_name'],
                'DB_USERNAME' => $data['db_user'],
                'DB_PASSWORD' => $data['db_password'],
            ]);

            // 3️⃣ Set a new runtime connection for migrations
            config([
                'database.connections.installer' => [
                    'driver' => 'mysql',
                    'host' => $data['db_host'],
                    'port' => $data['db_port'],
                    'database' => $data['db_name'],
                    'username' => $data['db_user'],
                    'password' => $data['db_password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                ],
                'database.default' => 'installer', // <-- explicitly use installer connection
            ]);

            // 4️⃣ Clear cache just in case
            Artisan::call('config:clear');

            // 5️⃣ Run migrations using the installer connection
            Artisan::call('migrate:fresh', [
                '--force' => true,
                '--database' => 'installer', // <-- ensure it uses the correct connection
            ]);

            // 6️⃣ Seeders (use the same connection)
            Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true, '--database' => 'installer']);
            Artisan::call('db:seed', ['--class' => 'PermissionSeeder', '--force' => true, '--database' => 'installer']);
            Artisan::call('db:seed', ['--class' => 'UserSeeder', '--force' => true, '--database' => 'installer']);

            return redirect()->route('install.finish')->with('success', 'Database connected and created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['db_error' => 'Connection failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Update .env values.
     */
    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $env = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=\"" . addslashes($value) . "\"";
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $replacement, $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $env);
    }

    public function finish()
    {
        // 1️⃣ Generate APP_KEY if missing
        $key = 'base64:' . base64_encode(random_bytes(32));

        // Update .env
        $envPath = base_path('.env');
        $env = file_exists($envPath) ? file_get_contents($envPath) : '';

        // Variables to update
        $variables = [
            'APP_KEY' => $key,
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'database',
            'QUEUE_CONNECTION' => 'database',
        ];

        foreach ($variables as $var => $value) {
            if (preg_match("/^{$var}=.*/m", $env)) {
                $env = preg_replace("/^{$var}=.*/m", "{$var}={$value}", $env);
            } else {
                $env .= "\n{$var}={$value}";
            }
        }

        file_put_contents($envPath, $env);

        // Set runtime configuration
        config([
            'app.key' => $key,
            'session.driver' => 'database',
            'cache.default' => 'database',
            'queue.default' => 'database',
        ]);

        // Create installation lock file
        $installedFile = storage_path('installed');
        if (!file_exists($installedFile)) {
            file_put_contents($installedFile, now());
        }

        return view('installer::install.finish');
    }
}
