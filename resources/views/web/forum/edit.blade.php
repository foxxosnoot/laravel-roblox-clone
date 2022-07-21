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

@section('css')
    <style>
        img.user-headshot {
            background: var(--section_bg);
            border-radius: 6px;
            max-width: 140%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <ul class="breadcrumb bg-white">
                <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
                <li class="breadcrumb-item active">Edit {{ ucfirst($type) }}</li>
            </ul>
            <div class="card">
                <div class="card-header bg-primary text-white">{{ $title }}</div>
                <div class="card-body">
                    <form action="{{ route('forum.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="type" value="{{ $type }}">

                        @if ($post->quote)
                            <div class="card has-bg">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-1 hide-sm">
                                            <a href="{{ route('users.profile', $post->quote->creator->username) }}">
                                                <img class="user-headshot" src="{{ $post->quote->creator->headshot() }}">
                                            </a>
                                        </div>
                                        <div class="col-md-11">
                                            <div>{{ $post->quote->created_at->diffForHumans() }}, <a href="{{ route('users.profile', $post->quote->creator->username) }}">{{ $post->quote->creator->username }}</a> wrote:</div>
                                            <div class="text-italic">{!! nl2br(e($post->quote->body)) !!}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($type == 'thread')
                            <label for="title">Title</label>
                            <input class="form-control mb-2" type="text" name="title" placeholder="Title" value="{{ $post->title }}" required>
                        @endif
                        <label for="body">Body</label>
                        <textarea class="form-control mb-3" name="body" placeholder="Write your post here..." rows="5" required>{{ $post->body }}</textarea>
                        <button class="btn btn-block btn-success" type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
