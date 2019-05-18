<html>
<head>
    <link rel="stylesheet" href="https://cdn.buildapart.io/css/tachyons.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
        @supports (font-variation-settings: normal) {
            html { font-family: 'Inter var', sans-serif; }
        }
    </style>
</head>
<body>
<div class="tc">
    @if($success ?? false)
        <div class="bg-green white br2 pa3 w-100">{{ $success->message }}</div>
    @else
        <div class="bg-red white br2 pa3 w-100">{{ $errors->message }}</div>
    @endif
</div>
</body>
</html>

