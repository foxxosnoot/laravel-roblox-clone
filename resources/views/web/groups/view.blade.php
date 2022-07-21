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
    'title' => $group->name,
    'image' => $group->thumbnail()
])

@section('meta')
    <meta name="group-info" data-id="{{ $group->id }}" data-can-moderate-wall="{{ Auth::check() && Auth::user()->id == $group->owner->id }}">
@endsection

@section('css')
    <style>
        .group-tabs .nav-link {
            border-radius: 0;
        }

        .group-tabs .nav-link:not(.active):hover {
            background: var(--section_bg_hover);
        }

        .group-tabs li:first-child .nav-link {
            border-radius: 8px 8px 0 0;
        }

        .group-tabs li:last-child .nav-link {
            border-radius: 0 0 8px 8px;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('js/group.js?v=3') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body" style="padding:15px;">
                    <img src="{{ $group->thumbnail() }}">
                </div>
            </div>
            <div class="mb-3 text-center">
                <h4>
                    @if (Auth::check() && Auth::user()->isInGroup($group->id))
                        <form action="{{ route('groups.set_primary') }}" method="POST" style="display:inline-block;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $group->id }}">
                            <button class="mr-1" style="background:none;font-size:18px;border:none;outline:none;appearance:none;padding:0;" type="submit">
                                <i class="{{ (Auth::user()->primary_group_id == $group->id) ? 'fas' : 'fal' }} fa-star text-warning"></i>
                            </button>
                        </form>
                    @endif
                    <span>{{ $group->name }}</span>
                </h4>
                <div>Owner: <a href="{{ route('users.profile', $group->owner->username) }}">{{ $group->owner->username }}</a></div>
            </div>
            @auth
                <div class="mb-4">
                    @if (Auth::user()->id != $group->owner->id)
                        <form action="{{ route('groups.membership') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $group->id }}">

                            @if ($group->is_private && $isPending)
                                <button class="btn btn-block btn-secondary" disabled>Pending</button>
                            @elseif (!Auth::user()->isInGroup($group->id))
                                <button class="btn btn-block btn-success" type="submit">Join</button>
                            @else
                                <button class="btn btn-block btn-danger" type="submit">Leave</button>
                            @endif
                        </form>
                    @else
                        <a href="{{ route('groups.manage', [$group->id, $group->slug()]) }}" class="btn btn-block btn-primary mb-2">Manage</a>
                        <a href="{{ route('creator_area.index', ['gid' => $group->id]) }}" class="btn btn-block btn-success">Create</a>
                    @endif
                </div>
            @endauth
            <div class="card">
                <ul class="group-tabs nav nav-pills nav-justified flex-column flex-row">
                    <li class="nav-item">
                        <span href="#aboutTab" class="nav-link flex-fill text-left active" data-toggle="tab">About</span>
                    </li>
                    <li class="nav-item">
                        <span href="#membersTab" class="nav-link flex-fill text-left" data-toggle="tab">Members</span>
                    </li>
                    <li class="nav-item">
                        <span href="#marketTab" class="nav-link flex-fill text-left" data-toggle="tab">Market</span>
                    </li>
                </ul>
            </div>
            <div class="card text-center">
                <div class="card-body">
                    <h5>{{ number_format($group->members()->count()) }}</h5>
                    <h6 class="text-muted" style="margin-top:-10px;margin-bottom:0;">MEMBERS</h6>
                    @if ($group->is_vault_viewable)
                        <h5 class="mt-2"><i class="currency"></i> {{ number_format($group->vault) }}</h5>
                        <h6 class="text-muted" style="margin-top:-10px;margin-bottom:0;">VAULT</h6>
                    @endif
                    @if (Auth::check() && Auth::user()->isInGroup($group->id))
                        <h5 class="mt-2">{{ Auth::user()->rankInGroup($group->id)->name }}</h5>
                        <h6 class="text-muted" style="margin-top:-10px;margin-bottom:0;">RANK</h6>
                    @endif
                </div>
            </div>
            @if (Auth::check() && $group->owner->id != Auth::user()->id && !$group->owner->isStaff())
                <div class="text-right">
                    <a href="{{ route('report.index', ['group', $group->id]) }}" class="text-danger">
                        <i class="fas fa-flag"></i>
                        <span>Report</span>
                    </a>
                </div>
                <div class="mb-1 show-sm-only"></div>
            @endif
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane active show" id="aboutTab">
                    <h3>About</h3>
                    <div class="card">
                        <div class="card-body">
                            <div style="max-height:300px;overflow-y:auto;">
                                {!! (!empty($group->description)) ? nl2br(e($group->description)) : '<div class="text-muted">This group does not have a description.</div>' !!}
                            </div>
                        </div>
                    </div>
                    @if (Auth::check() && Auth::user()->isInGroup($group->id))
                    <h3>Wall</h3>
                    <div class="card">
                        <div class="card-body">
                            <form id="wallPost">
                                <textarea class="form-control mb-3" name="body" placeholder="Body" rows="3" minlength="3" maxlength="150"></textarea>
                                <p class="text-danger" id="wallPostError"></p>
                                <button class="btn btn-success" type="submit">Post</button>
                            </form>
                            <hr>
                            <div id="wall"></div>
                        </div>
                    </div>
                @endif
                </div>
                <div class="tab-pane" id="membersTab">
                    <div class="row">
                        <div class="col">
                            <h3>Members</h3>
                        </div>
                        <div class="col text-right">
                            <select class="form-control">
                                @foreach ($group->ranks() as $rank)
                                    <option value="{{ $rank->rank }}">{{ $rank->name }} ({{ $rank->memberCount() }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row" id="members"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="marketTab">
                    <h3>Market</h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="row" id="items"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
