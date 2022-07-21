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
    'title' => $thread->title,
    'image' => $thread->creator->headshot()
])

@section('css')
    <style>
        img.user-headshot {
            background: var(--section_bg);
            border-radius: 6px;
            width: 50px;
        }

        .primary-group {
            background: var(--section_bg_inside);
            font-weight: 600;
            border-radius: 4px;
            padding: 3px 10px;
        }

        .primary-group a {
            color: inherit;
            font-size: 12px;
        }

        .primary-group .rank {
            font-size: 11px;
            margin-top: -2px;
            margin-bottom: 5px;
        }

        .primary-group img {
            border-radius: 6px;
            max-width: 250%;
        }

        @media only screen and (max-width: 768px) {
            .primary-group img {
                max-width: 35%;
                margin-top: 5px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            <h3>Forum</h3>
        </div>
        <div class="col-9 text-right">
            @if (!Auth::check() || (Auth::check() && (!$thread->is_locked || Auth::user()->isStaff() && $thread->is_locked)))
                <a href="{{ route('forum.new', ['reply', $thread->id]) }}" class="btn btn-success">Reply</a>
            @else
                <button class="btn btn-success" disabled>Reply</button>
            @endif
        </div>
    </div>
    @if ($thread->is_deleted)
        <div class="alert bg-danger text-white text-center">This thread is deleted.</div>
    @endif
    <ul class="breadcrumb bg-white">
        <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
        <li class="breadcrumb-item"><a href="{{ route('forum.topic', [$thread->topic->id, $thread->topic->slug()]) }}">{{ $thread->topic->name }}</a></li>
        <li class="breadcrumb-item active">Thread</li>
    </ul>
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">
                @if ($thread->is_pinned) <i class="fas fa-thumbtack mr-1"></i> @endif
                @if ($thread->is_locked) <i class="fas fa-lock mr-1"></i> @endif
                <span>{{ $thread->title }}</span>
            </h5>

            @if ($thread->replies()->currentPage() == 1)
                <div class="card" style="border:none;">
                    <div class="card-header bg-primary text-white" style="padding:.75rem 4px;border:none;border-top-left-radius:0px!important;border-top-right-radius:0px!important;">
                        <div class="row">
                            <div class="col-4 col-md-2 text-center">
                                <span>Poster</span>
                            </div>
                            <div class="col-8 col-md-10">
                                <span>Body</span>
                                @if (Auth::check() && $thread->creator->id != Auth::user()->id && !$thread->creator->isStaff())
                                    <a href="{{ route('report.index', ['forum-thread', $thread->id]) }}" class="float-right mr-3" style="color:inherit;"><i class="fas fa-flag"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:15px 0;">
                        <div class="row">
                            <div class="col-4 col-md-2 text-center">
                                <div class="text-truncate mb-1">
                                    <div class="bg-{{ ($thread->creator->online()) ? 'success' : 'muted' }} mr-1" style="height:9px;width:9px;border-radius:50px;display:inline-block;"></div>
                                    <a href="{{ route('users.profile', $thread->creator->username) }}" style="color:inherit;">{{ $thread->creator->username }}</a>
                                    @if ($thread->creator->is_verified)
                                        <i class="fas fa-shield-check text-success ml-1" style="font-size:13px;" title="This user is verified." data-toggle="tooltip"></i>
                                    @endif
                                </div>
                                <a href="{{ route('users.profile', $thread->creator->username) }}">
                                    <img src="{{ $thread->creator->thumbnail() }}">
                                </a>

                                @if ($thread->creator->isStaff())
                                    <div class="badge text-white bg-danger mt-2 d-block pt-1 pb-1" style="font-size:16px;border-radius:8px 0 8px 0;"><strong>Admin</strong></div>
                                @elseif($thread->creator->hasMembership())
                                    <div class="badge mt-2 d-block pt-1 pb-1" style="color:{{ config('site.membership_color') }};background:{{ config('site.membership_bg_color') }};font-size:16px;border-radius:8px 0 8px 0;"><strong>{{ config('site.membership_name') }}</strong></div>
                                @endif

                                @if ($thread->creator->hasPrimaryGroup())
                                    <div class="primary-group mt-2 text-center-sm">
                                        <div class="row">
                                            <div class="col-md-3 text-center align-self-center">
                                                <a href="{{ route('groups.view', [$thread->creator->primaryGroup->id, $thread->creator->primaryGroup->slug()]) }}">
                                                    <img src="{{ $thread->creator->primaryGroup->thumbnail() }}">
                                                </a>
                                            </div>
                                            <div class="col-md-9 text-center">
                                                <div class="text-truncate">
                                                    <a href="{{ route('groups.view', [$thread->creator->primaryGroup->id, $thread->creator->primaryGroup->slug()]) }}">{{ $thread->creator->primaryGroup->name }}</a>
                                                    <div class="rank text-muted">{{ $thread->creator->rankInGroup($thread->creator->primaryGroup->id)->name }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mt-1">
                                    <div class="col-md-6"><strong>Level:</strong></div>
                                    <div class="col-md-6 text-right hide-sm">{{ $thread->creator->forum_level }}</div>
                                    <div class="col-md-6 show-sm-only">{{ $thread->creator->forum_level }}</div>
                                </div>
                                <div class="mb-1 show-sm-only"></div>
                                <div class="row">
                                    <div class="col-md-6"><strong>Posts:</strong></div>
                                    <div class="col-md-6 text-right hide-sm">{{ number_format($thread->creator->forumPostCount()) }}</div>
                                    <div class="col-md-6 show-sm-only">{{ number_format($thread->creator->forumPostCount()) }}</div>
                                </div>
                            </div>
                            <div class="col-8 col-md-10">
                                <div class="text-muted" style="font-size:14px;margin-bottom:5px;"><i class="fas fa-clock"></i> Posted {{ $thread->created_at->diffForHumans() }}</div>
                                <div>{!! nl2br(e($thread->body)) !!}</div>

                                @if ($thread->creator->forum_signature)
                                    <hr>
                                    <div class="text-muted" style="font-size:14px;">{{ $thread->creator->forum_signature }}</div>
                                @endif

                                @if (Auth::check() && Auth::user()->isStaff())
                                    @if (
                                        Auth::user()->staff('can_delete_forum_posts') ||
                                        Auth::user()->staff('can_edit_forum_posts') ||
                                        Auth::user()->staff('can_pin_forum_posts') ||
                                        Auth::user()->staff('can_lock_forum_posts')
                                    ) {!! (!$thread->creator->forum_signature) ? '<hr>' : '<div class="mt-3"></div>' !!}  @endif

                                    @if (Auth::user()->staff('can_delete_forum_posts'))
                                        <a href="{{ route('forum.moderate', ['thread', 'delete', $thread->id]) }}" class="btn btn-sm btn-{{ (!$thread->is_deleted) ? 'primary' : 'danger' }}"><i class="fas fa-trash" style="width:13px;"></i></a>
                                    @endif

                                    @if (Auth::user()->staff('can_edit_forum_posts'))
                                        <a href="{{ route('forum.edit', ['thread', $thread->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit" style="width:13px;"></i></a>
                                    @endif

                                    @if (Auth::user()->staff('can_pin_forum_posts'))
                                        <a href="{{ route('forum.moderate', ['thread', 'pin', $thread->id]) }}" class="btn btn-sm btn-{{ (!$thread->is_pinned) ? 'primary' : 'danger' }}"><i class="fas fa-thumbtack" style="width:13px;"></i></a>
                                    @endif

                                    @if (Auth::user()->staff('can_lock_forum_posts'))
                                        <a href="{{ route('forum.moderate', ['thread', 'lock', $thread->id]) }}" class="btn btn-sm btn-{{ (!$thread->is_locked) ? 'primary' : 'danger' }}"><i class="fas fa-lock" style="width:13px;"></i></a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @foreach ($thread->replies() as $reply)
                <div class="card" style="border:none;@if ($reply->is_deleted)opacity:0.5;@endif">
                    <div class="card-header bg-primary text-white" style="padding:.75rem 4px;border:none;border-top-left-radius:0px!important;border-top-right-radius:0px!important;">
                        <div class="row">
                            <div class="col-4 col-md-2 text-center">
                                <span>Poster</span>
                            </div>
                            <div class="col-8 col-md-10">
                                <span>Body</span>
                                @auth
                                    @if (!$thread->is_locked || (Auth::user()->isStaff() && $thread->is_locked))
                                        <a href="{{ route('forum.new', ['quote', $reply->id]) }}" class="float-right mr-3" style="color:inherit;"><i class="fas fa-quote-left"></i></a>
                                    @endif

                                    @if ($reply->creator->id != Auth::user()->id && !$reply->creator->isStaff())
                                        <a href="{{ route('report.index', ['forum-reply', $reply->id]) }}" class="float-right mr-3" style="color:inherit;"><i class="fas fa-flag"></i></a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:15px 0;">
                        <div class="row">
                            <div class="col-4 col-md-2 text-center">
                                <div class="text-truncate mb-1">
                                    <div class="bg-{{ ($reply->creator->online()) ? 'success' : 'muted' }} mr-1" style="height:9px;width:9px;border-radius:50px;display:inline-block;"></div>
                                    <a href="{{ route('users.profile', $reply->creator->username) }}" style="color:inherit;">{{ $reply->creator->username }}</a>
                                    @if ($reply->creator->is_verified)
                                        <i class="fas fa-shield-check text-success ml-1" style="font-size:13px;" title="This user is verified." data-toggle="tooltip"></i>
                                    @endif
                                </div>
                                <a href="{{ route('users.profile', $reply->creator->username) }}">
                                    <img src="{{ $reply->creator->thumbnail() }}">
                                </a>

                                @if ($reply->creator->isStaff())
                                    <div class="badge text-white bg-danger mt-2 d-block pt-1 pb-1" style="font-size:16px;border-radius:8px 0 8px 0;"><strong>Admin</strong></div>
                                @elseif($reply->creator->hasMembership())
                                    <div class="badge mt-2 d-block pt-1 pb-1" style="color:{{ config('site.membership_color') }};background:{{ config('site.membership_bg_color') }};font-size:16px;border-radius:8px 0 8px 0;"><strong>{{ config('site.membership_name') }}</strong></div>
                                @endif

                                @if ($reply->creator->hasPrimaryGroup())
                                    <div class="primary-group mt-2 text-center-sm">
                                        <div class="row">
                                            <div class="col-md-3 text-center align-self-center">
                                                <a href="{{ route('groups.view', [$reply->creator->primaryGroup->id, $reply->creator->primaryGroup->slug()]) }}">
                                                    <img src="{{ $reply->creator->primaryGroup->thumbnail() }}">
                                                </a>
                                            </div>
                                            <div class="col-md-9 text-center">
                                                <div class="text-truncate">
                                                    <a href="{{ route('groups.view', [$reply->creator->primaryGroup->id, $reply->creator->primaryGroup->slug()]) }}">{{ $reply->creator->primaryGroup->name }}</a>
                                                    <div class="rank text-muted">{{ $reply->creator->rankInGroup($reply->creator->primaryGroup->id)->name }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mt-1">
                                    <div class="col-md-6"><strong>Level:</strong></div>
                                    <div class="col-md-6 text-right hide-sm">{{ $reply->creator->forum_level }}</div>
                                    <div class="col-md-6 show-sm-only">{{ $reply->creator->forum_level }}</div>
                                </div>
                                <div class="mb-1 show-sm-only"></div>
                                <div class="row">
                                    <div class="col-md-6"><strong>Posts:</strong></div>
                                    <div class="col-md-6 text-right hide-sm">{{ number_format($reply->creator->forumPostCount()) }}</div>
                                    <div class="col-md-6 show-sm-only">{{ number_format($reply->creator->forumPostCount()) }}</div>
                                </div>
                            </div>
                            <div class="col-8 col-md-10">
                                <div class="text-muted" style="font-size:14px;margin-bottom:5px;"><i class="fas fa-clock"></i> Posted {{ $reply->created_at->diffForHumans() }}</div>

                                @if ($reply->quote_id && (!$reply->quote->is_deleted || (Auth::check() && Auth::user()->isStaff())))
                                    <div class="card has-bg">
                                        <div class="card-body">
                                            <div class="row no-gutters">
                                                <div class="col-auto pr-3 hide-sm">
                                                    <a href="{{ route('users.profile', $reply->quote->creator->username) }}">
                                                        <img class="user-headshot" src="{{ $reply->quote->creator->headshot() }}">
                                                    </a>
                                                </div>
                                                <div class="col">
                                                    <div>{{ $reply->quote->created_at->diffForHumans() }}, <a href="{{ route('users.profile', $reply->quote->creator->username) }}">{{ $reply->quote->creator->username }}</a> wrote:</div>
                                                    <div class="text-italic">{!! nl2br(e($reply->quote->body)) !!}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div>{!! nl2br(e($reply->body)) !!}</div>

                                @if ($reply->creator->forum_signature)
                                    <hr>
                                    <div class="text-muted" style="font-size:14px;">{{ $reply->creator->forum_signature }}</div>
                                @endif

                                @if (Auth::check() && Auth::user()->isStaff())
                                    @if (
                                        Auth::user()->staff('can_delete_forum_posts') ||
                                        Auth::user()->staff('can_edit_forum_posts')
                                    ) {!! (!$reply->creator->forum_signature) ? '<hr>' : '<div class="mt-3"></div>' !!} @endif

                                    @if (Auth::user()->staff('can_delete_forum_posts'))
                                        <a href="{{ route('forum.moderate', ['reply', 'delete', $reply->id]) }}" class="btn btn-sm btn-{{ (!$reply->is_deleted) ? 'primary' : 'danger' }}"><i class="fas fa-trash" style="width:13px;"></i></a>
                                    @endif

                                    @if (Auth::user()->staff('can_edit_forum_posts'))
                                        <a href="{{ route('forum.edit', ['reply', $reply->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit" style="width:13px;"></i></a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
    {{ $thread->replies()->onEachSide(1) }}
@endsection
