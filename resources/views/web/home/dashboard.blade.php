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
    'title' => 'Dashboard'
])

@section('css')
    <style>
        img.user-headshot {
            background: var(--headshot_bg);
            border-radius: 50%;
        }

        .update {
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .update:not(:last-child) {
            border-bottom: 1px solid var(--divider_color);
        }

        @media only screen and (max-width: 768px) {
            img.user-headshot {
                width: 40%;
                margin-bottom: 16px
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <h3>Dashboard</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <a href="{{ route('users.profile', Auth::user()->username) }}">
                                <img class="user-headshot" src="{{ Auth::user()->headshot() }}">
                            </a>
                            <a href="{{ route('account.invite.index') }}" class="btn btn-block btn-primary mt-3 mb-3">Invite</a>
                        </div>
                        <div class="col-md-9">
                            <h4 class="text-center-sm">Welcome, {{ Auth::user()->username }}</h4>
                            <form action="{{ route('account.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="category" value="general">
                                <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                                <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                                <label for="description">Description</label>
                                <textarea class="form-control mb-3" name="description" placeholder="Hi there, my name is {{ Auth::user()->username }}!" rows="5">{{ Auth::user()->description }}</textarea>
                                <button class="btn btn-success" type="submit">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <h3>Updates</h3>
            <div class="card">
                <div class="card-body" @if ($updates->count() > 0) style="padding-top:0;padding-bottom:0;" @endif>
                    @forelse ($updates as $update)
                        <div class="update">
                            <div class="text-truncate"><a href="{{ route('forum.thread', $update->id) }}" style="color:inherit;font-weight:600;">{{ $update->title }}</a></div>
                            <div class="text-muted text-truncate">by <a href="{{ route('users.profile', $update->creator->username) }}">{{ $update->creator->username }}</a> {{ $update->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <p>No updates found.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col">
                    <h3>Recent Items</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-success">View All</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @forelse ($items as $item)
                            <div class="col-6 col-md-2">
                                <div class="card mb-sm-only" style="border:none;">
                                    <div class="card-body" style="padding:0;">
                                        <a href="{{ route('catalog.item', [$item->id, $item->slug()]) }}" style="color:inherit;font-weight:600;">
                                            @if ($item->limited)
                                                <div class="bg-primary text-white text-center" style="border-radius:50%;width:30px;height:30px;position:absolute;margin-left:5px;margin-top:5px;">
                                                    <span style="font-size:20px;font-weight:600;margin-top:7px;">C</span>
                                                </div>
                                            @elseif ($item->isTimed())
                                                <div class="bg-danger text-white text-center" style="border-radius:50%;width:30px;height:30px;position:absolute;margin-left:5px;margin-top:5px;">
                                                    <span style="font-size:17px;font-weight:600;"><i class="fas fa-clock" style="margin-top:6.5px;"></i></span>
                                                </div>
                                            @endif
                                            <img style="background:var(--section_bg_inside);border-radius:6px;padding:{{ itemTypePadding($item->type) }};" src="{{ $item->thumbnail() }}">
                                            <div class="text-truncate mt-1">{{ $item->name }}</div>
                                        </a>

                                        @if ($item->onsale() && $item->price == 0)
                                            <span class="text-success">Free</span>
                                        @elseif (!$item->onsale())
                                            <span class="text-muted">Off Sale</span>
                                        @else
                                            <span><i class="currency"></i> {{ number_format($item->price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col">No items found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
