<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kyle Parisi's blog">

    <title>Kyle Parisi</title>

    <!-- Styles -->
    <link rel="stylesheet" href="/css/tachyons.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
        @supports (font-variation-settings: normal) {
            html { font-family: 'Inter var', sans-serif; }
        }
    </style>

    <!-- Scripts -->
    <script src="/js/mousetrap.min.js"></script>
    <script src="/js/jquery-3.4.1.min.js"></script>
    <script src="/js/autosize.min.js"></script>
    <script src="/js/lolight.min.js"></script>
    <script src="/js/intense.min.js"></script>
</head>
<body>

    <div class="min-vh-100">
        <div class="w-80-l w-90 pt5 center">
            @yield('content')
        </div>
    </div>

    @include('includes.footer')
</body>
</html>
