<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.buildapart.io/css/tachyons.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <title>Subscribe</title>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
        @supports (font-variation-settings: normal) {
            html { font-family: 'Inter var', sans-serif; }
        }
    </style>
</head>
<body>
@if($success ?? false)
    <div class="bg-green white br2 pa3 w-100">{{ $success->message }}</div>
@else
    <form method="post">
        <div class="flex-l items-end-l">
            <div class="pb3 w-40-l">
                <div>
                    <label for="firstName" class="f6">First Name</label>
                </div>
                <input title="firstName" name="firstName" type="text"
                       class="input-reset ba b--black-10 pa2 bg-light-gray b0 w-95-l w-100"
                       placeholder="John Doe"
                />
            </div>
            <div class="pb3 w-40-l">
                <div>
                    <label for="email" class="f6">Email <span class="red">{{ $errors->email ?? "" }}</span></label>
                </div>
                <input title="email" name="email" type="email"
                       class="input-reset ba b--black-10 pa2 bg-light-gray b0 w-95-l w-100"
                       placeholder="johndoe@gmail.com"
                />
            </div>
            <div class="pb3 w-20-l">
                <button type="submit" class="button w-100">Subscribe</button>
            </div>
        </div>
    </form>
@endif
</body>
</html>

