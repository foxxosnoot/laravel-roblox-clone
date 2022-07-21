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
    'title' => "{$user->username}'s Profile",
    'image' => $user->headshot()
])

@section('meta')
    <meta name="item-types-with-padding" content="{{ json_encode(config('site.item_thumbnails_with_padding')) }}">
    <meta name="item-type-padding-amount" content="{{ itemTypePadding('default') }}">
    <meta name="user-info" data-id="{{ $user->id }}" data-inventory-public="{{ $user->setting->public_inventory }}">
@endsection

@section('css')
    <style>
        .description {
            height: 225px;
            overflow-y: auto;
        }

        @media only screen and (max-width: 768px) {
            .description {
                height: auto;
                max-height: 225px;
            }
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('js/profile.js?v=4') }}"></script>
@endsection

@section('content')
    @if ($user->isBanned())
        <div class="alert bg-danger text-white text-center">
            <span>This user is banned.</span>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="{{ $user->thumbnail() }}" width="80%">

                            @if (Auth::check() && $user->id != Auth::user()->id)
                                <div class="row mt-3">
                                    @if ($user->setting->accepts_messages)
                                        <div class="col">
                                            <a href="{{ route('account.inbox.new', ['message', $user->username]) }}" class="btn btn-block btn-primary"><i class="fas fa-envelope"></i></a>
                                        </div>
                                    @endif

                                    @if ($areFriends || $isPending || $user->setting->accepts_friends)
                                        <div class="col">
                                            @if ($areFriends)
                                                <form action="{{ route('account.friends.update') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                                    <input type="hidden" name="action" value="remove">
                                                    <button class="btn btn-block btn-danger"><i class="fas fa-user-times"></i></button>
                                                </form>
                                            @elseif ($isPending)
                                                <button class="btn btn-block btn-secondary" disabled><i class="fas fa-clock"></i></button>
                                            @elseif ($user->setting->accepts_friends)
                                                <form action="{{ route('account.friends.update') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                                    <input type="hidden" name="action" value="send">
                                                    <button class="btn btn-block btn-success"><i class="fas fa-user-plus"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($user->setting->accepts_trades && !$user->isBanned())
                                        <div class="col">
                                            <a href="{{ route('account.trades.send', $user->username) }}" class="btn btn-block btn-warning"><i class="fas fa-exchange"></i></a>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if (Auth::check() && Auth::user()->isStaff() && Auth::user()->staff('can_view_user_info'))
                                <a href="{{ route('admin.users.view', $user->id) }}" class="btn btn-block btn-danger mt-2" target="_blank"><i class="fas fa-gavel"></i> View in Panel</a>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div style="font-size:28px;">
                                <span>{{ $user->username }}</span>

                                @if ($user->is_verified)
                                    <i class="fas fa-shield-check text-success ml-1" style="font-size:16px;" title="This user is verified." data-toggle="tooltip"></i>
                                @endif

                                @if ($user->usernameHistory()->count() > 0)
                                    <i class="fal fa-info-circle text-muted ml-1" style="font-size:16px;" title="Previous Usernames: {{ $user->usernameHistoryString() }}" data-toggle="tooltip"></i>
                                @endif

                                <span style="{{ ($user->online()) ? 'background:var(--success);color:#fff;' : 'background:#c7c7c7;color:#333;' }}font-size:14px;text-align:center;margin-left:5px;vertical-align:middle;border-radius:3px;padding:2px 5px;">{{ ($user->online()) ? 'Online' : 'Offline' }}</span>
                            </div>
                            <div class="description">
                                {!! (!empty($user->description)) ? nl2br(e($user->description)) : '<div class="text-muted">This user does not have a description.</div>' !!}
                            </div>
                            @if (Auth::check() && $user->id != Auth::user()->id && !$user->isStaff() && !$user->isBanned())
                                <div class="text-right">
                                    <a href="{{ route('report.index', ['user', $user->username]) }}" class="text-danger">
                                        <i class="fas fa-flag"></i>
                                        <span>Report</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="row text-center">
                        <div class="col-6 col-md">
                            <h5>{{ $user->created_at->format('M d, Y') }}</h5>
                            <h6 class="text-muted" style="margin-top:-10px;">JOIN DATE</h6>
                        </div>
                        <div class="col-6 col-md">
                            <h5>{!! ($user->online()) ? '<span class="text-success">Right Now</span>' : $user->updated_at->format('M d, Y') !!}</h5>
                            <h6 class="text-muted" style="margin-top:-10px;">LAST SEEN</h6>
                        </div>
                        <div class="col-6 col-md">
                            <h5>{{ number_format($user->forumPostCount()) }}</h5>
                            <h6 class="text-muted" style="margin-top:-10px;">FORUM POSTS</h6>
                        </div>
                        <div class="col-6 col-md">
                            <h5>{{ number_format($user->friends()->count()) }}</h5>
                            <h6 class="text-muted" style="margin-top:-10px;">FRIENDS</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($user->badges()))
            <div class="col-md-12">
                <h3>Badges</h3>
                <div class="card">
                    <div class="card-body" style="padding-bottom:0;">
                        <div class="row">
                            @foreach ($user->badges() as $badge)
                                <div class="col-4 col-md-2 text-center">
                                    <div class="card" style="border:none;" title="{{ $badge->name }}" data-toggle="tooltip">
                                        <a href="{{ route('badges.index') }}">
                                            <img src="{{ $badge->image }}" style="background:var(--section_bg_inside);border-radius:6px;">
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="row">
                <div class="col">
                    <h3>Friends</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('users.friends', $user->username) }}" class="btn btn-sm btn-success">View All</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body" @if ($friends->count() > 0) style="padding-bottom:0;" @endif>
                    <div class="row">
                        @forelse ($friends as $friend)
                            <div class="col-6 col-md-4 text-center">
                                <div class="card" style="border:none;">
                                    <a href="{{ route('users.profile', $friend->username) }}" style="color:inherit;">
                                        <img src="{{ $friend->thumbnail() }}">
                                        <div class="text-truncate mt-1"><strong>{{ $friend->username }}</strong></div>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col text-center">
                                <i class="fas fa-frown text-warning mb-2" style="font-size:50px;"></i>
                                <div>This user has no friends.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col">
                    <h3>Groups</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('users.groups', $user->username) }}" class="btn btn-sm btn-success">View All</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body" @if ($groups->count() > 0) style="padding-bottom:0;" @endif>
                    <div class="row">
                        @forelse ($groups as $group)
                            <div class="col-6 col-md-4 text-center">
                                <div class="card" style="border:none;">
                                    <a href="{{ route('groups.view', [$group->id, $group->slug()]) }}" style="color:inherit;">
                                        <img src="{{ $group->thumbnail() }}">
                                        <div class="text-truncate mt-1"><strong>{{ $group->name }}</strong></div>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col text-center">
                                <i class="fas fa-frown text-warning mb-2" style="font-size:50px;"></i>
                                <div>This user is not in any groups.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @if ($user->setting->public_inventory)
            <div class="col-md-12">
                <h3>Inventory</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <ul class="nav nav-pills nav-justified flex-column flex-row" role="tablist">
                                    @foreach (config('site.inventory_item_types') as $type)
                                        <li class="nav-item">
                                            <span class="nav-link flex-fill text-left @if ($type == 'hat') active @endif" data-category="{{ lcfirst(itemType($type, true)) }}">{{ itemType($type, true) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mb-2 show-sm-only"></div>
                            </div>
                            <div class="col-md-10">
                                <div class="row" id="inventory"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
