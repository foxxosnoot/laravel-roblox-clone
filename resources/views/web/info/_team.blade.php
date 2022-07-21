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

@section('css')
    <style>
        .user:not(:first-child) {
            padding-top: 15px;
        }

        .user:not(:last-child) {
            padding-bottom: 15px;
            border-bottom: 1px solid var(--divider_color);
        }
    </style>
@endsection

@forelse ($users as $user)
    <div class="row user">
        <div class="col-4 col-md-2 text-center">
            <a href="{{ route('users.profile', $user->username) }}">
                <img src="{{ $user->thumbnail() }}">
            </a>
        </div>
        <div class="col-8 col-md-10">
            <h5 class="mb-2 text-truncate"><a href="{{ route('users.profile', $user->username) }}">{{ $user->username }}</a></h5>
            <div class="text-muted">{{ $user->description ?? 'This user does not have a description.' }}</div>
        </div>
    </div>
@empty
    <p>No team members found.</p>
@endforelse
