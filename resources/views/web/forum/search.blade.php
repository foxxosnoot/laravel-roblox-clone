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
    'title' => 'Search'
])

@section('css')
    <style>
        .thread {
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .thread:not(:last-child) {
            border-bottom: 1px solid var(--divider_color);
        }

        .thread:hover {
            background: var(--section_bg_hover);
        }

        .thread .user-headshot {
            width: 50px;
            height: 50px;
            float: left;
            position: relative;
            overflow: hidden;
        }

        .thread .user-headshot img {
            background: var(--headshot_bg);
            border-radius: 50%;
        }

        .thread .details {
            padding-left: 25px;
        }

        .thread .status {
            font-size: 11px;
            border-radius: 4px;
            margin-right: 5px;
            padding: 0.5px 5px;
            font-weight: 600;
            display: inline-block;
        }

        .thread .status i {
            font-size: 10px;
            vertical-align: middle;
        }

        .thread .status i.fa-lock {
            margin-top: -1px;
        }
    </style>
@endsection

@section('content')
    <h3>Forum</h3>
    <ul class="breadcrumb bg-white">
        <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
        <li class="breadcrumb-item active">Search</li>
    </ul>
    <form action="{{ route('forum.search') }}" method="GET">
        <input class="form-control mb-3" type="text" name="search" placeholder="Search..." value="{{ request()->search }}">
    </form>
    @if (!empty($search))
        @if ($threads->count() == 0)
            <p>No threads have been found.</p>
        @else
            <div class="card">
                <div class="card-header bg-primary text-white" style="padding-left:15px;padding-right:15px;">
                    <div class="row">
                        <div class="col-md-8">Post</div>
                        <div class="col-md-2 text-center hide-sm">Replies</div>
                        <div class="col-md-2 text-center hide-sm">Last Reply</div>
                    </div>
                </div>
                <div class="card-body" style="padding-top:0;padding-left:15px;padding-right:15px;padding-bottom:0;">
                    @foreach ($threads as $thread)
                        <div class="row thread">
                            <div class="col-md-8">
                                <div class="user-headshot">
                                    <img src="{{ $thread->creator->headshot() }}" width="150px">
                                </div>
                                <div class="details text-truncate">
                                    <a href="{{ route('forum.thread', $thread->id) }}" style="color:inherit;font-size:18px;font-weight:600;text-decoration:none;">{{ $thread->title }}</a>
                                    <div class="text-muted" style="margin-top:-3px;">
                                        @if ($thread->is_pinned)
                                            <span class="status bg-danger text-white"><i class="fas fa-thumbtack mr-1"></i> Pinned</span>
                                        @elseif ($thread->is_locked)
                                            <span class="status text-white" style="background:#000;"><i class="fas fa-lock mr-1"></i> Locked</span>
                                        @endif

                                        <span class="hide-sm">Posted by</span>
                                        <a href="{{ route('users.profile', $thread->creator->username) }}" @if ($thread->creator->isStaff()) class="text-danger" @endif>{{ $thread->creator->username }}</a>
                                        <span>- {{ $thread->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-center align-self-center hide-sm">{{ number_format($thread->replies(false)->count()) }}</div>
                            <div class="col-md-2 text-center align-self-center hide-sm">
                                @if ($thread->lastReply())
                                    <a href="{{ route('users.profile', $thread->lastReply()->creator->username) }}" @if ($thread->lastReply()->creator->isStaff()) class="text-danger" @endif>{{ $thread->lastReply()->creator->username }}</a>
                                    <div>{{ $thread->lastReply()->created_at->diffForHumans() }}</div>
                                @else
                                    <a href="{{ route('users.profile', $thread->creator->username) }}" @if ($thread->creator->isStaff()) class="text-danger" @endif>{{ $thread->creator->username }}</a>
                                    <div>{{ $thread->created_at->diffForHumans() }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{ $threads->onEachSide(1) }}
        @endif
    @endif
@endsection
