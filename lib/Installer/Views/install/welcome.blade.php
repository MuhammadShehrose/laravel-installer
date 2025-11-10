<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        <h2 class="mb-4">Welcome to the Installer</h2>
                        <p class="text-muted mb-4">
                            This setup will guide you through installing and configuring your Loyalty & Referral system.
                        </p>
                        <a href="{{ route('install.check') }}" class="btn btn-primary px-4 py-2">
                            Start Installation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
