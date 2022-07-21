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
    'title' => 'Verify Discord Account'
])

@section('css')
    @if (empty(Auth::user()->discord_id) && !empty(Auth::user()->discord_code))
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    @endif
@endsection

@section('js')
    @if (empty(Auth::user()->discord_id) && !empty(Auth::user()->discord_code))
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script>
            $(function() {
                $('input[name="code"]').click(function() {
                    this.select();
                    document.execCommand('copy');

                    toastr.success('Code copied to clipboard!');
                });
            });
        </script>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fab fa-discord mb-2" style="color:#7289da;font-size:80px;"></i>
                    <h4>Verify your Discord Account</h4>
                    @if (empty(Auth::user()->discord_id))
                        @if (empty(Auth::user()->discord_code))
                            <p>Click the 'Generate' button below to generate a unique key which you will then DM to our bot.</p>
                            <form action="{{ route('account.discord.generate') }}" method="POST">
                                @csrf
                                <button class="btn btn-success">Generate Code</button>
                            </form>
                        @else
                            <p>To finish this process, DM the code posted below to our verification bot.</p>
                            <input class="form-control" style="cursor:pointer;" type="text" name="code" placeholder="Discord Code" value="{{ Auth::user()->discord_code }}" readonly>
                        @endif
                    @else
                        <p>If you would like to unlink your Discord account, click the 'Unlink' button.</p>
                        <form action="{{ route('account.discord.generate') }}" method="POST">
                            @csrf
                            <button class="btn btn-danger">Unlink Account</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
