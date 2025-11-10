# 🧭 Laravel Custom Installer

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**A powerful step-by-step installer for Laravel applications**

*Streamline your deployment with automated environment setup, database configuration, and more.*

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Troubleshooting](#-troubleshooting)

</div>

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🔧 **Smart Environment Setup**
- Automatic `.env` generation from template
- Environment validation and verification
- Secure configuration management

### 🗄️ **Database Management**
- Interactive database configuration
- Automatic database creation
- One-click migrations & seeders

</td>
<td width="50%">

### ✅ **System Validation**
- PHP version & extension checks
- Directory permission verification
- Comprehensive requirement scanning

### 🔒 **Security Features**
- Installation lock mechanism
- Middleware protection layers
- Auto-generated APP_KEY

</td>
</tr>
</table>

---

## 📂 Directory Structure

```
laravel-project/
│
├── 📁 app/
│   └── 📁 Http/
│       └── 📁 Middleware/
│           └── 📄 CheckInstallation.php
│
├── 📁 lib/
│   └── 📁 Installer/
│       ├── 📁 Controllers/
│       │   └── 📄 InstallerController.php
│       │
│       ├── 📁 Middleware/
│       │   └── 📄 RedirectIfNotInstalled.php
│       │
│       ├── 📁 Routes/
│       │   └── 📄 install.php
│       │
│       └── 📁 Views/
│           └── 📁 install/
│               ├── 📄 welcome.blade.php
│               ├── 📄 check.blade.php
│               ├── 📄 database.blade.php
│               └── 📄 finish.blade.php
│
├── 📁 public/
│   └── 📄 index.php (modified)
│
└── 📄 .env.installer (template file)
```

---

## 🚀 Installation

### Step 1: Copy Installer Files

Copy all the provided files into your Laravel project according to the directory structure above.

### Step 2: Register PSR-4 Namespace

Edit your `composer.json` and add the installer namespace:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Installer\\": "lib/Installer/"
        }
    }
}
```

Then run:

```bash
composer dump-autoload
```

### Step 3: Register Middleware

In `bootstrap/app.php`, register the installer middleware aliases:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        // Your existing middleware...
        
        // 👇 Installer Middleware
        'check.installation' => \App\Http\Middleware\CheckInstallation::class,
        'redirect.if.not.installed' => Installer\Middleware\RedirectIfNotInstalled::class,
    ]);
})
```

### Step 4: Modify `public/index.php`

Replace your `public/index.php` with:

```php
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
```

> **💡 Note:** This ensures your app uses `.env.installer` when `.env` is missing.

### Step 5: Load Installer Routes

In your `routes/web.php`, include the installer routes:

```php
require base_path('lib/Installer/Routes/install.php');
```

---

## 🎯 Usage

### Installation Flow

<table>
<thead>
<tr>
<th width="10%">Step</th>
<th width="30%">Screen</th>
<th width="60%">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td align="center"><strong>1️⃣</strong></td>
<td><strong>Welcome</strong></td>
<td>Checks or creates <code>.env</code> from <code>.env.installer</code> template</td>
</tr>
<tr>
<td align="center"><strong>2️⃣</strong></td>
<td><strong>System Check</strong></td>
<td>Verifies PHP version, required extensions, and writable directories</td>
</tr>
<tr>
<td align="center"><strong>3️⃣</strong></td>
<td><strong>Database Setup</strong></td>
<td>Collects credentials, creates database, runs migrations & seeders</td>
</tr>
<tr>
<td align="center"><strong>4️⃣</strong></td>
<td><strong>Finish</strong></td>
<td>Generates <code>APP_KEY</code>, updates environment, and locks installer</td>
</tr>
</tbody>
</table>

### Accessing the Installer

1. **First Time:** Navigate to your application URL (e.g., `http://localhost/your-project`)
2. **Auto-Redirect:** You'll be automatically redirected to `/install/welcome`
3. **Follow Steps:** Complete each step in the installation wizard

---

## 🔐 Middleware Protection

### `CheckInstallation.php`
- ✅ Blocks access to `/install/*` routes after installation
- ✅ Redirects to homepage if `storage/installed` exists
- ✅ Prevents re-installation attempts

### `RedirectIfNotInstalled.php`
- ✅ Redirects all routes to installer if `.env` or `storage/installed` is missing
- ✅ Allows access to `/install/*` and `/api/*` routes
- ✅ Ensures installation before application use

---

## 📋 Default Environment Template

The `.env.installer` file serves as your environment template. Ensure it includes:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Add your other environment variables...
```

---

## ✅ After Installation

Once installation completes:

- ✅ `.env` is created with your database credentials
- ✅ `APP_KEY` is automatically generated
- ✅ `storage/installed` lock file is created
- ✅ Application is ready to use

### Re-running the Installer

To reinstall, delete these files:

```bash
rm storage/installed
rm .env
```

Then refresh your browser.

---

## 🛠️ Troubleshooting

<details>
<summary><strong>❌ Database Connection Error</strong></summary>

**Error:** `Connection failed: SQLSTATE[HY000] [1045]...`

**Solution:**
- Verify database credentials (username, password, host)
- Ensure MySQL/MariaDB service is running
- Check database user permissions

</details>

<details>
<summary><strong>🔄 Infinite Redirect Loop</strong></summary>

**Solution:**
- Verify both middleware are correctly registered in `bootstrap/app.php`
- Check if `.env` and `storage/installed` exist/don't exist based on current state
- Clear application cache: `php artisan optimize:clear`

</details>

<details>
<summary><strong>⚪ White Screen / Blank Page</strong></summary>

**Solution:**
```bash
composer dump-autoload
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
```

Check Laravel logs in `storage/logs/laravel.log`

</details>

<details>
<summary><strong>📝 Installer Routes Not Found</strong></summary>

**Solution:**
- Verify `require base_path('lib/Installer/Routes/install.php');` is in `routes/web.php`
- Ensure folder name is `Routes` (capital R), not `Route`
- Run `composer dump-autoload`

</details>

---

## 🎨 Customization

### Modifying Views

All installer views are located in `lib/Installer/Views/install/`:

- `welcome.blade.php` - Welcome screen
- `check.blade.php` - System requirements check
- `database.blade.php` - Database configuration
- `finish.blade.php` - Installation complete

Feel free to customize these views to match your application's branding.

### Extending Functionality

The `InstallerController.php` can be extended to add:
- Custom validation rules
- Additional setup steps
- Email configuration
- Queue setup
- And more...

---

## 👨‍💻 Credits

**Developed by Muhammad Shehrose**

A lightweight, reusable, and production-ready Laravel installer module designed to simplify deployment and setup processes.

---

## 📜 License

This project is licensed under the **MIT License**.

```
MIT License

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software...
```

---

<div align="center">

### ⭐ If you find this useful, please consider giving it a star!

**Made with ❤️ for the Laravel Community**

</div>
