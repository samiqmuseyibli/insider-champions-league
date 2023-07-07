<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Insider Champions League</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        input{
            max-width: 50px;
        }
        .container div > div.row{
            background-color: rgba(0, 0, 0, 0.13);
        }
    </style>
</head>

<body>
<main class="d-flex align-items-center">
    <div class="container">
        @yield('content')
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
@yield('scripts')
</body>

</html>
