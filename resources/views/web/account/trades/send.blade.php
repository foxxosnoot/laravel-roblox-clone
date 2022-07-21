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
    'title' => "Send Trade to {$user->username}"
])

@section('meta')
    <meta name="routes" data-process="{{ route('account.trades.process') }}">
    <meta name="trade-info" data-receiver="{{ $user->id }}">
@endsection

@section('js')
    <script src="{{ asset('js/trade.js?v=3') }}"></script>
@endsection

@section('content')
    <h3>Send Trade to {{ $user->username }}</h3>
    <div class="row">
        <div class="col-md-6">
            <h3>Giving</h3>
            <div class="card">
                <div class="card-body" style="max-height:650px;overflow-y:auto;">
                    <h5>Currency</h5>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text" style="background:var(--section_bg_inside);">
                                <i class="currency"></i>
                            </div>
                        </div>
                        <input class="form-control" id="givingCurrency" type="number" placeholder="Currency">
                    </div>
                    <h5>Items</h5>
                    <div class="row">
                        @forelse ($giving as $item)
                            <div class="col-6 col-md-4">
                                <div class="card" id="item_{{ $item->id }}" style="border:none;cursor:pointer;" onclick="addItem({{ $item->id }}, 'giving')">
                                    <img style="background:var(--section_bg_inside);border:2px solid var(--section_bg);border-radius:6px;padding:{{ itemTypePadding($item->type) }};" src="{{ $item->thumbnail() }}">
                                    <div class="text-truncate"><strong>{{ $item->name }}</strong></div>
                                </div>
                            </div>
                        @empty
                            <div class="col">You do not have any items to trade with.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Receiving</h3>
            <div class="card">
                <div class="card-body" style="max-height:650px;overflow-y:auto;">
                    <h5>Currency</h5>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text" style="background:var(--section_bg_inside);">
                                <i class="currency"></i>
                            </div>
                        </div>
                        <input class="form-control" id="receivingCurrency" type="number" placeholder="Currency">
                    </div>
                    <h5>Items</h5>
                    <div class="row">
                        @forelse ($receiving as $item)
                            <div class="col-6 col-md-4">
                                <div class="card" id="item_{{ $item->id }}" style="border:none;cursor:pointer;" onclick="addItem({{ $item->id }}, 'receiving')">
                                    <img style="background:var(--section_bg_inside);border:2px solid var(--section_bg);border-radius:6px;padding:{{ itemTypePadding($item->type) }};" src="{{ $item->thumbnail() }}">
                                    <div class="text-truncate"><strong>{{ $item->name }}</strong></div>
                                </div>
                            </div>
                        @empty
                            <div class="col">This user does not have any items to trade with.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button class="btn btn-block btn-success" id="sendButton">Send Trade</button>
        </div>
    </div>

    <div class="modal fade" id="error" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p id="errorText"></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
