<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Meu Projeto Laravel')</title>

    <!-- Bootstrap CSS via CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Meu Projeto</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('football.index') }}">Principal</a>
                        <a class="nav-link" href="{{ route('football.times') }}">Times</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="p-4 container" style="height: 100%;">
        @yield('content')
    </main>

    <footer class="bg-dark text-light text-center py-3 mt-4">
        &copy; {{ date('Y') }} - Meu Projeto Laravel
    </footer>

    <!-- Bootstrap JS via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>

<style>
    html, body {
        font-family: 'Nunito', sans-serif;
        height: 100%;
    }

    footer {
        left: 0;
        bottom: 0;
    }

    .card-custom {
        border-radius: 15px;
        padding: 20px;
        background-color: #f8f9fa;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .btn-team {
        width: 100%;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: start;
        border: none;
        background: transparent;
        padding: 10px;
    }
    .btn-team img {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }
</style>
</html>
