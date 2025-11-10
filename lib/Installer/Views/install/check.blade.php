<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer - System Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h3 class="mb-4 text-center">System Requirements Check</h3>

                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Requirement</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PHP Version (>= 8.2)</td>
                                    <td>
                                        @if ($requirements['php'])
                                            <span class="badge bg-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>

                                @foreach ($requirements['extensions'] as $ext => $ok)
                                    <tr>
                                        <td>{{ strtoupper($ext) }} Extension</td>
                                        <td>
                                            @if ($ok)
                                                <span class="badge bg-success">Enabled</span>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach ($requirements['permissions'] as $path => $ok)
                                    <tr>
                                        <td>{{ $path }} Writable</td>
                                        <td>
                                            @if ($ok)
                                                <span class="badge bg-success">Writable</span>
                                            @else
                                                <span class="badge bg-danger">Not Writable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-center mt-4">
                            @if ($allPassed)
                                <a href="{{ route('install.database') }}" class="btn btn-success px-4 py-2">Continue</a>
                            @else
                                <button class="btn btn-secondary px-4 py-2" disabled>Fix Issues to Continue</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
