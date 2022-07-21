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
    'title' => 'Upgrade'
])

@section('content')
    <h3>{{ config('site.membership_name') }} Membership</h3>
    <div class="row text-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <img src="{{ $image }}" width="48%">
                    <div class="mb-3"></div>
                    <a href="{{ route('account.upgrade.checkout', str_replace('_', '-', $products['membership']['item_name'])) }}" class="btn" style="background:{{ config('site.membership_bg_color') }};color:{{ config('site.membership_color') }};" type="submit" @if (Auth::user()->hasMembership()) disabled @endif>Buy for ${{ $products['membership']['price'] }}/month</a>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div>Join and create {{ config('site.group_limit_membership') }} groups instead of {{ config('site.group_limit') }}</div>
                    <hr>
                    <div>{{ config('site.daily_currency_membership') }} daily currency instead of {{ config('site.daily_currency') }}</div>
                    <hr>
                    <div>{{ config('site.membership_name') }} tag below your username on the forums</div>
                    <hr>
                    <div>Donator and Membership badges</div>
                    <hr>
                    <div>Donator item</div>
                </div>
            </div>
        </div>
    </div>
    <h3>Currency</h3>
    <div class="row justify-content-center">
        @forelse ($products['currency'] as $product)
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2" style="font-weight:600;"><i class="currency"></i> {{ $product['display_name'] }}</h4>
                        <a href="{{ route('account.upgrade.checkout', str_replace('_', '-', $product['item_name'])) }}" class="btn btn-success" type="submit">Buy for ${{ $product['price'] }}</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col">No currency products found.</div>
        @endforelse
    </div>
@endsection
