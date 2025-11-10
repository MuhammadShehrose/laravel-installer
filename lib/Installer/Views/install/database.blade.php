<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer - Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h3 class="mb-4 text-center">Database Configuration</h3>

                        @if ($errors->has('db_error'))
                            <div class="alert alert-danger">{{ $errors->first('db_error') }}</div>
                        @endif

                        <form action="{{ route('install.database') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="db_host" class="form-label">Database Host</label>
                                <input type="text" class="form-control" id="db_host" name="db_host"
                                    value="{{ old('db_host', '127.0.0.1') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="db_port" class="form-label">Database Port</label>
                                <input type="number" class="form-control" id="db_port" name="db_port"
                                    value="{{ old('db_port', 3306) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="db_name" class="form-label">Database Name</label>
                                <input type="text" class="form-control" id="db_name" name="db_name"
                                    value="{{ old('db_name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="db_user" class="form-label">Database Username</label>
                                <input type="text" class="form-control" id="db_user" name="db_user"
                                    value="{{ old('db_user') }}" required autocomplete="new-username">
                            </div>

                            <div class="mb-3">
                                <label for="db_password" class="form-label">Database Password</label>
                                <input type="password" class="form-control" id="db_password" name="db_password"
                                    value="{{ old('db_password') }}" autocomplete="new-password">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Test & Save</button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <small class="text-muted">Ensure the database exists before continuing.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
