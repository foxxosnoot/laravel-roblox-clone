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
    'title' => "Ban {$user->username}"
])

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.ban.create') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <label for="length">Length</label>
                <select class="form-control mb-2" name="length" required>
                    @foreach ($lengths as $name => $value)
                        <option value="{{ $value }}">{{ $name }}</option>
                    @endforeach
                </select>
                <label for="category">Category</label>
                <select class="form-control mb-2" name="category" required>
                    @foreach ($categories as $name => $value)
                        <option value="{{ $value }}">{{ $name }}</option>
                    @endforeach
                </select>
                <label for="note">Note (optional)</label>
                <textarea class="form-control mb-3" name="note" placeholder="Note" rows="5"></textarea>
                <button class="btn btn-block btn-success" type="submit">Ban User</button>
            </form>
        </div>
    </div>
@endsection
