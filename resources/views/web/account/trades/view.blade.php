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
    'title' => 'Trade'
])

@section('content')
    <div class="row">
        <div class="col">
            <h3>{{ (Auth::user()->id == $trade->receiver->id) ? "Trade sent by {$trade->sender->username}" : "Trade sent to {$trade->receiver->username}" }}</h3>
        </div>
        @if ($trade->status == 'pending')
            <div class="col text-right hide-sm">
                <form action="{{ route('account.trades.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $trade->id }}">
                    @if (Auth::user()->id == $trade->receiver->id)
                        <button class="btn btn-success mr-2" name="action" value="accept">Accept</button>
                    @endif
                    <button class="btn btn-danger" name="action" value="decline">Decline</button>
                </form>
            </div>
        @endif
    </div>
    <div class="row">
        @if (Auth::user()->id == $trade->receiver->id)
            @include('web.account.trades._section', [
                'title' => ($trade->status != 'pending') ? 'You Gave' : 'You Are Giving',
                'items' => $receiving,
                'currency' => $trade->receiving_currency
            ])

            @include('web.account.trades._section', [
                'title' => ($trade->status != 'pending') ? 'You Received' : 'You Are Receiving',
                'items' => $giving,
                'currency' => $trade->giving_currency
            ])
        @else
            @include('web.account.trades._section', [
                'title' => ($trade->status != 'pending') ? 'You Gave' : 'You Are Giving',
                'items' => $giving,
                'currency' => $trade->giving_currency
            ])

            @include('web.account.trades._section', [
                'title' => ($trade->status != 'pending') ? 'You Received' : 'You Are Receiving',
                'items' => $receiving,
                'currency' => $trade->receiving_currency
            ])
        @endif

    @if ($trade->status == 'pending')
        <div class="col text-right show-sm-only">
            <form action="{{ route('account.trades.process') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $trade->id }}">
                <div class="row">
                    @if (Auth::user()->id == $trade->receiver->id)
                        <div class="col">
                            <button class="btn btn-block btn-success" name="action" value="accept">Accept</button>
                        </div>
                    @endif
                    <div class="col">
                        <button class="btn btn-block btn-danger" name="action" value="decline">Decline</button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="col-md-12">This trade has been {{ $trade->status }}.</div>
    @endif
@endsection
