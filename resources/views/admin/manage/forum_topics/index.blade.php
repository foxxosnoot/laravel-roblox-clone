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
    'title' => 'Manage Forum Topics'
])

@section('header')
    <a href="{{ route('admin.manage.forum_topics.new') }}" class="btn btn-success"><i class="fas fa-plus"></i></a>
@endsection

@section('content')
    @if ($topics->count() == 0)
        <p>No forum topics were found.</p>
    @else
        <div class="card" style="border:0;">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Created</th>
                        <th>Posts</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topics as $topic)
                        <tr>
                            <td><a href="{{ route('admin.manage.forum_topics.edit', $topic->id) }}">{{ $topic->id }}</a></td>
                            <td><a href="{{ route('admin.manage.forum_topics.edit', $topic->id) }}">{{ $topic->name }}</a></td>
                            <td>{{ $topic->created_at }}</td>
                            <td>{{ number_format($topic->threads()->count()) }}</td>
                            <td><a href="{{ route('admin.manage.forum_topics.confirm_delete', $topic->id) }}" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $topics->onEachSide(1) }}
    @endif
@endsection
