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

@extends('layouts.default', [
    'title' => $title,
    'image' => $icon
])

@section('css')
    <style>
        @media only screen and (max-width: 768px) {
            img.referrer {
                width: 50%;
            }
        }
    </style>
@endsection

@section('js')
    @if (config('app.env') == 'production' && site_setting('registration_enabled'))
        {!! NoCaptcha::renderJs() !!}
    @endif
@endsection

@section('content')
    @if (!site_setting('registration_enabled'))
        <p>Account creation is currently disabled.</p>
    @else
        <div class="row">
            <div class="col-md-4 offset-md-1 text-center hide-sm">
                <img class="referrer mb-2" src="{{ $image }}">
                <div class="font-italic mb-2">{!! $text !!}</div>
            </div>
            <div class="col-md-6">
                <h3>Register</h3>
                <div class="card">
                    <div class="card-body">
                        @if ($referred)
                            <div class="show-sm-only text-center">
                                <img class="referrer mb-2" src="{{ $image }}">
                                <div class="font-italic">{!! $text !!}</div>
                                <hr>
                            </div>
                        @endif
                        <form action="{{ route('auth.register.authenticate') }}" method="POST">
                            @csrf

                            @if ($referred)
                                <input type="hidden" name="referral_code" value="{{ $referralCode }}">
                            @endif

                            <label for="username">Username</label>
                            <input class="form-control mb-2" type="text" name="username" placeholder="Username" required>
                            <label for="email">Email Address</label>
                            <input class="form-control mb-2" type="email" name="email" placeholder="Email Address" required>
                            <label for="password">Password</label>
                            <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
                            <label for="password_confirmation">Confirm Password</label>
                            <input class="form-control mb-{{ (config('app.env') == 'production') ? '2' : '3' }}" type="password" name="password_confirmation" placeholder="Confirm Password" required>
                            @if (config('app.env') == 'production')
                                <div class="mt-3 mb-3">
                                    {!! NoCaptcha::display(['data-theme' => 'dark']) !!}
                                </div>
                            @endif
                            <button class="btn btn-block btn-success">Register</button>
                            <hr>
                            <div class="text-center">Already have an account? <a href="{{ route('auth.login.index') }}">Login</a></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
