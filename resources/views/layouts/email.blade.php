<!--
MIT License

Copyright (c) 2021-2022 FoxxoSnoot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | {{ config('site.name') }}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind:400,500,600,700">
    <style>
        body {
            background: #ededed;
            color: #333;
            font-family: 'Hind', sans-serif;
            font-size: 15px;
        }

        a {
            color: #039be5!important;
            text-decoration: none!important;
        }

        p {
            margin: 0;
        }

        p:not(:last-child) {
            margin-bottom: 16px;
        }

        .title {
            font-size: 25px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .content {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 2px;
            padding: 25px;
        }

        .footer {
            margin-top: 25px;
            margin-bottom: 10px;
            text-align: center;
        }

        .footer .sender {
            opacity: .8;
        }
        
        .footer .powered-by {
            font-size: 13px;
            opacity: .6;
        }
    </style>
</head>
<body>
    <div class="title">{{ $title }}</div>
    <div class="content">
        @yield('content')
    </div>
    <div class="footer">
        <div class="sender">&mdash; Team {{ config('site.name') }}</div>
        <div class="powered-by">Powered by <a href="https://github.com/FoxxoSnoot/laravel-roblox-clone" target="_blank">Laravel Roblox Clone</a></div>
    </div>
</body>
</html>
