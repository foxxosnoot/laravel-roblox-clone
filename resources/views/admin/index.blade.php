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

@extends('layouts.admin', [
    'title' => 'Home'
])

@section('content')
    <div class="row">
        @forelse ($options as $option)
            <div class="col-6 col-md-3 text-center">
                <a href="{{ $option[0] }}" style="color:{{ $option[3] }};text-decoration:none;">
                    <div class="card">
                        <div class="card-body">
                            <i class="{{ $option[2] }} mb-2" style="font-size:60px;"></i>
                            <div class="text-truncate" style="font-weight:600;">{{ $option[1] }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col">You do not have access to any admin panel features.</div>
        @endforelse
    </div>
@endsection
