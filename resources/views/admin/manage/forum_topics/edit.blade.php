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
    'title' => "Edit \"{$topic->name}\""
])

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.manage.forum_topics.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $topic->id }}">
                <label for="name">Name</label>
                <input class="form-control mb-2" type="text" name="name" placeholder="Name" value="{{ $topic->name }}" required>
                <label for="description">Description</label>
                <textarea class="form-control mb-2" name="description" placeholder="Write topic description here..." rows="5" required>{{ $topic->description }}</textarea>
                <label for="home_page_priority">Home Page Priority</label>
                <input class="form-control mb-2" type="number" name="home_page_priority" placeholder="Home Page Priority" value="{{ $topic->home_page_priority }}" min="1" max="255" required>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_staff_only_viewing" @if ($topic->is_staff_only_viewing) checked @endif>
                    <label class="form-check-label" for="staff_only_viewing">Staff Only Viewing</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_staff_only_posting" @if ($topic->is_staff_only_posting) checked @endif>
                    <label class="form-check-label" for="staff_only_posting">Staff Only Posting</label>
                </div>
                <button class="btn btn-block btn-success" type="submit">Edit</button>
            </form>
        </div>
    </div>
@endsection
