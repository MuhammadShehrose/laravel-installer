# 🧭 Laravel Custom Installer

A simple yet powerful **step-by-step installer** for Laravel applications.  
It automatically handles `.env` setup, database configuration, migrations, and seeders — all through a clean browser interface.

---

## 🚀 Features

- Environment setup from `.env.installer`
- System requirements & permission checks
- Interactive database configuration (with automatic DB creation)
- Auto-run of migrations and seeders
- Auto-generation of `APP_KEY`
- Installer lock mechanism (`storage/installed`)
- Middleware protection for installed and non-installed states

---

## 📁 Directory Structure

The installer files should be placed exactly like this:

root
├── app
│ └── Http
│ └── Middleware
│ └── CheckInstallation.php
│
├── lib
│ └── Installer
│ ├── Controllers
│ │ └── InstallerController.php
│ ├── Middleware
│ │ └── RedirectIfNotInstalled.php
│ ├── Route
│ │ └── install.php
│ └── Views
│ └── install
│ ├── check.blade.php
│ ├── database.blade.php
│ ├── finish.blade.php
│ └── welcome.blade.php
│
├── .env.installer
└── public
└── index.php

yaml
Copy code

---

## ⚙️ Step-by-Step Installation

### 1️⃣ Copy Installer Files

Copy the provided files and folders into your **Laravel project root** exactly as shown above.

---

### 2️⃣ Register PSR-4 Namespace

In your project’s `composer.json`, add the installer namespace under the `autoload.psr-4` section:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Installer\\": "lib/Installer/"
    }
}
Then run:

bash
Copy code
composer dump-autoload
3️⃣ Register Installer Middlewares
In your bootstrap/app.php, inside the withMiddleware() block, register the installer middleware aliases:

php
Copy code
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'permission' => App\Http\Middleware\PermissionMiddleware::class,
        'require.location' => \App\Http\Middleware\RequireLocation::class,
        'active.user' => \App\Http\Middleware\CheckActiveUser::class,
        'verify.api.client' => \App\Http\Middleware\VerifyApiClient::class,

        // 👇 Installer Middleware
        'check.installation' => \App\Http\Middleware\CheckInstallation::class,
        'redirect.if.not.installed' => Installer\Middleware\RedirectIfNotInstalled::class,
    ]);

    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
})
4️⃣ Modify public/index.php
Replace your default public/index.php with this version:

php
Copy code
<?php

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
This ensures your app uses .env.installer when .env is missing — enabling first-time installation.

5️⃣ Load Installer Routes
In your routes/web.php, include the installer routes:

php
Copy code
require base_path('lib/Installer/Route/install.php');
🧩 Installer Flow
Step	Description	View
1. Welcome	Checks or creates .env from .env.installer	install/welcome.blade.php
2. System Check	Verifies PHP version, extensions, and writable directories	install/check.blade.php
3. Database Setup	Collects DB credentials, creates DB, runs migrations & seeders	install/database.blade.php
4. Finish	Generates APP_KEY, updates environment, and locks installer	install/finish.blade.php

🔐 Middleware Logic
CheckInstallation.php
Blocks /install/* routes after installation.

Redirects to / if storage/installed exists.

RedirectIfNotInstalled.php
Redirects all routes to installer if .env or storage/installed is missing.

Skips redirects for install/* and api/*.

🧾 Default .env.installer
This file acts as the environment template for the first-time setup.
It includes basic placeholders for database, mail, and app configuration.
Make sure it exists at the project root (.env.installer).

⚡ After Installation
Once the installer finishes:

.env is updated with database credentials and app settings.

APP_KEY is generated.

storage/installed file is created to lock the installer.

To re-run the installer, delete:

bash
Copy code
storage/installed
.env
Then reload your app in the browser.

🧰 Troubleshooting
Error: “Connection failed: SQLSTATE[HY000] [1045]…”
→ Verify your database credentials and ensure MySQL is running.

Installer redirects infinitely
→ Check that both middlewares are correctly registered and your .env / storage/installed exist or not based on state.

White screen / blank page
→ Run composer dump-autoload and clear caches:

bash
Copy code
php artisan optimize:clear
🧑‍💻 Credits
Developed by [Shehrose] — A lightweight and reusable Laravel installer module.
You’re free to modify, extend, or package it for your own Laravel applications.

📜 License
This installer is released under the MIT License.
Feel free to use and adapt it in commercial or personal Laravel projects.