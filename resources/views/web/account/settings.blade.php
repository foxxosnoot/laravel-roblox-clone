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
    'title' => 'Settings'
])

@section('content')
    <h3>{{ ucfirst($category) }} Settings</h3>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills nav-justified" role="tablist">
                <li class="nav-item">
                    <a href="{{ route('account.settings.index', 'general') }}" class="nav-link @if ($category == 'general') active @endif">General</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('account.settings.index', 'privacy') }}" class="nav-link @if ($category == 'privacy') active @endif">Privacy</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('account.settings.index', 'password') }}" class="nav-link @if ($category == 'password') active @endif">Password</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('account.settings.index', 'appearance') }}" class="nav-link @if ($category == 'appearance') active @endif">Appearance</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('account.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="category" value="{{ $category }}">

                @if ($category == 'general')
                    <h3>Information</h3>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-4 col-md-2 align-self-center"><strong>User ID:</strong></div>
                        <div class="col-8 col-md-10 mt-1 mb-1">
                            <input class="form-control" type="number" placeholder="ID" value="{{ Auth::user()->id }}" disabled>
                        </div>
                        <div class="col-4 col-md-2 align-self-center"><strong>Username:</strong></div>
                        <div class="col-8 col-md-10 mt-1 mb-1">
                            <input class="form-control" type="text" name="username" placeholder="Username" value="{{ Auth::user()->username }}">
                        </div>
                        <div class="col-4 col-md-2 align-self-center"><strong>Email:</strong></div>
                        <div class="col-8 col-md-10 mt-1 mb-1">
                            <input class="form-control" type="email" name="email" placeholder="Email" value="{{ Auth::user()->email }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Description</h3>
                            <hr>
                            <textarea class="form-control mb-3" name="description" placeholder="Hi there, my name is {{ Auth::user()->username }}!" rows="5">{{ Auth::user()->description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <h3>Forum Signature</h3>
                            <hr>
                            <input class="form-control mb-3" name="forum_signature" placeholder="Forum Signature" value="{{ Auth::user()->forum_signature }}">
                        </div>
                    </div>
                    <button class="btn btn-success" type="submit">Update</button>
                @elseif ($category == 'privacy')
                    <h3>Privacy & Security</h3>
                    <hr>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accepts_messages" @if (Auth::user()->setting->accepts_messages) checked @endif>
                        <label class="form-check-label" for="accepts_messages">Accepts Messages</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accepts_friends" @if (Auth::user()->setting->accepts_friends) checked @endif>
                        <label class="form-check-label" for="accepts_friends">Accepts Friends</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accepts_trades" @if (Auth::user()->setting->accepts_trades) checked @endif>
                        <label class="form-check-label" for="accepts_trades">Accepts Trades</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="public_inventory" @if (Auth::user()->setting->public_inventory) checked @endif>
                        <label class="form-check-label" for="public_inventory">Public Inventory</label>
                    </div>
                    <button class="btn btn-success" type="submit">Update</button>
                @elseif ($category == 'password')
                    <h3>Change Password</h3>
                    <hr>
                    <label for="current_password">Current Password</label>
                    <input class="form-control mb-2" type="password" name="current_password" placeholder="Current Password" required>
                    <label for="new_password">New Password</label>
                    <input class="form-control mb-2" type="password" name="new_password" placeholder="New Password" required>
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input class="form-control mb-3" type="password" name="new_password_confirmation" placeholder="Confirm New Password" required>
                    <button class="btn btn-success" type="submit">Change</button>
                @elseif ($category == 'appearance')
                    <h3>Appearance</h3>
                    <hr>
                    <select class="form-control mb-3" name="theme">
                        @foreach ($themes as $theme)
                            <option value="{{ $theme }}" @if (Auth::user()->setting->theme == $theme) selected @endif>{{ ucwords(str_replace('_', ' ', $theme)) }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-success" type="submit">Update</button>
                    <div class="mb-3"></div>
                    <small class="text-muted">More appearance settings coming soon.</small>
                @endif
            </form>
        </div>
    </div>
@endsection
