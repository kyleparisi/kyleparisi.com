<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="">

    <title></title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/tachyons.min.css">
    <link rel="stylesheet" href="css/app.css">
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
        @supports (font-variation-settings: normal) {
            html { font-family: 'Inter var', sans-serif; }
        }
    </style>

    <!-- Scripts -->

</head>
<body>
<div class="absolute top-0 left-0 bg-dark-blue w-100" style="height: 0.5rem"></div>

<nav class="w-80 pt5-ns center cf">

</nav>

<div class="min-vh-100">

    <div class="w-90 w-75-m w-third-l center mt5">
        <div class="pb4">
            <a href="/" class="link">
                <div class="flex items-center justify-center">
                    <div class="br2 ba bg-dark-blue w2 h2"></div>
                    <div class="pa2 lh-title gray fw6 f3 w3" style="line-height: 2rem;">App</div>
                </div>
            </a>
        </div>

        <div class="pa4">
            <h4 class="tc near-black fw1">Welcome Back!</h4>

            <div class="red pb2">{{ $errors->invalid ?? "" }}</div>
            <form method="post">
                <div class="pb3">
                    <div>
                        <label for="email" class="f6">Email</label>
                    </div>
                    <input title="email" name="email" type="email"
                           class="input-reset ba b--black-10 pa2 bg-light-gray b0 w-100"/>
                    <div class="red">{{ $errors->email ?? "" }}</div>
                </div>
                <div class="pb3">
                    <div>
                        <label for="password" class="f6">Password</label>
                    </div>
                    <input title="password" name="password" type="password"
                           class="input-reset ba b--black-10 pa2 bg-light-gray b0 w-100"/>
                    <div class="red">{{ $errors->password ?? "" }}</div>
                </div>
                <div class="pb3 flex items-center">
                    <input title="remember" name="remember" type="checkbox" class="mr2">
                    <label for="remember" class="f6">Remember me</label>
                </div>
                <div class="pb3">
                    <button type="submit" class="w-100">Login</button>
                </div>
            </form>

            <a href="/password/email" class="link blue">Forgot password?</a>
        </div>

        <div class="br2 br--bottom b--black-05 pa4 bg-light-gray">
            Don't have an account yet? <a href="sign-up" class="link blue">Sign up</a>
        </div>
    </div>

</div>

@include('includes.footer')

</body>
</html>
