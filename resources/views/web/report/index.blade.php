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
    'title' => $title
])

@section('content')
    <h3>{{ $title }}</h3>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <form action="{{ route('report.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $content->id }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <label for="category">How is this content breaking the {{ config('site.name') }} rules?</label>
                        <select class="form-control mb-2" name="category">
                            @foreach ($categories as $name => $value)
                                <option value="{{ $value }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <label for="comment">Leave a comment (optional)</label>
                        <textarea class="form-control mb-3" name="comment" placeholder="This content is breaking the rules by..." rows="5"></textarea>
                        <button class="btn btn-block btn-success" type="submit">Submit</button>
                    </form>
                    <div class="mb-3 show-sm-only"></div>
                </div>
                <div class="col-md-7">
                    <label>Info</label>
                    <div class="row">
                        @foreach ($fields as $name => $value)
                            <div class="col-md-2"><strong>{{ $name }}</strong></div>
                            <div class="col-md-10">
                                <div style="max-height:220px;overflow-y:auto;">{!! (!in_array($name, ['Creator', 'Owner', 'Sender'])) ? nl2br(e($value)) : $value !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
