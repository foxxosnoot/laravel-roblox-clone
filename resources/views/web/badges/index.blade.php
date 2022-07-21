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
    'title' => 'Badges'
])

@section('js')
    <script>
        function info(name, description, image)
        {
            $('#badge #badgeName').text(name);
            $('#badge #badgeDescription').text(description);
            $('#badge #badgeImage').attr('src', image);

            $('#badge').modal('show');
        }
    </script>
@endsection

@section('content')
    <h3>Badges</h3>
    <div class="card legal">
        <div class="card-body" @if (!empty($badges)) style="padding-bottom:0;" @endif>
            <div class="row">
                @forelse ($badges as $badge)
                    <div class="col-6 col-md-2 text-center">
                        <div class="card" style="border:none;cursor:pointer;" onclick="info('{{ $badge->name }}', '{{ $badge->description }}', '{{ $badge->image }}')">
                            <img src="{{ $badge->image }}" class="mb-2" style="background:var(--section_bg_inside);border-radius:6px;">
                            <div class="text-truncate"><strong>{{ $badge->name }}</strong></div>
                        </div>
                    </div>
                @empty
                    <div class="col">There are currently no badges.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="modal fade" id="badge" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="badgeName"></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <img id="badgeImage" width="100px">
                    <hr>
                    <p id="badgeDescription"></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
@endsection
