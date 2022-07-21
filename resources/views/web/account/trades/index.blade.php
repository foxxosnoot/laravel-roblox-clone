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
    'title' => 'Trades'
])

@section('css')
    <style>
        img.user-headshot {
            background: var(--section_bg);
            border-radius: 50%;
            margin: 0 auto;
            display: block;
            width: 70%;
        }
    </style>
@endsection

@section('content')
    <h3>Trades</h3>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills nav-justified" role="tablist">
                <li class="nav-item">
                    <a href="{{ route('account.trades.index', 'incoming') }}" class="nav-link @if ($category == 'incoming') active @endif">Incoming</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('account.trades.index', 'sent') }}" class="nav-link @if ($category == 'sent') active @endif">Sent</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('account.trades.index', 'history') }}" class="nav-link @if ($category == 'history') active @endif">History</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card">
        <div class="card-body" @if ($trades->count() > 0) style="padding-bottom:0;" @endif>
            @forelse ($trades as $trade)
                <div class="card has-bg text-center-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 hide-sm">
                                <a href="{{ route('users.profile', ($trade->receiver->id == Auth::user()->id) ? $trade->sender->username : $trade->receiver->username) }}">
                                    <img class="user-headshot" src="{{ ($trade->receiver->id == Auth::user()->id) ? $trade->sender->headshot() : $trade->receiver->headshot() }}">
                                </a>
                            </div>
                            <div class="col-md-8 align-self-center">
                                @if ($trade->receiver->id == Auth::user()->id)
                                    <h5>Sent by <a href="{{ route('users.profile', $trade->sender->username) }}">{{ $trade->sender->username }}</a></h5>
                                @else
                                    <h5>Sent to <a href="{{ route('users.profile', $trade->receiver->username) }}">{{ $trade->receiver->username }}</a></h5>
                                @endif

                                <h5 style="margin-bottom:0;">{{ $trade->created_at->diffForHumans() }}</h5>

                                @if ($category == 'history')
                                    <h5 class="mt-2 text-{{ ($trade->status == 'accepted') ? 'success' : 'danger' }}" style="margin-bottom:0;">{{ ucfirst($trade->status) }}</h5>
                                @endif

                                <div class="mb-3 show-sm-only"></div>
                            </div>
                            <div class="col-md-2 align-self-center text-right">
                                <a href="{{ route('account.trades.view', $trade->id) }}" class="btn btn-block btn-success">View Trade</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p>{{ $error }}</p>
            @endforelse
        </div>
    </div>
    {{ $trades->onEachSide(1) }}
@endsection
