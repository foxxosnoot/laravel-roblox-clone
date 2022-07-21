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
    'title' => 'Friend Requests'
])

@section('content')
    <h3>Friend Requests ({{ number_format($friendRequests->count()) }})</h3>
    <div class="card">
        <div class="card-body" @if ($friendRequests->count() > 0) style="padding-bottom:0;" @endif>
            <div class="row">
                @forelse ($friendRequests as $friendRequest)
                    <div class="col-6 col-md-2 text-center">
                        <div class="card" style="border:none;">
                            <a href="{{ route('users.profile', $friendRequest->sender->username) }}">
                                <img src="{{ $friendRequest->sender->thumbnail() }}">
                                <div class="text-truncate mt-1">{{ $friendRequest->sender->username }}</div>
                            </a>
                            <form action="{{ route('account.friends.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $friendRequest->sender->id }}">
                                <div class="row mt-2">
                                    <div class="col">
                                        <button class="btn btn-block btn-success" name="action" value="accept"><i class="fas fa-check"></i></button>
                                    </div>
                                    <div class="col">
                                        <button class="btn btn-block btn-danger" name="action" value="decline"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col">You currently have no incoming friend requests.</div>
                @endforelse
            </div>
        </div>
    </div>
    {{ $friendRequests->onEachSide(1) }}
@endsection
