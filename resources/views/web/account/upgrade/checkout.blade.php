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
    'title' => 'Checkout'
])

@section('content')
    <h3>Checkout</h3>
    <div class="card">
        <div class="card-body">
            <div class="row text-center-sm">
                <div class="col-md-2 text-center">
                    @if ($product['name'] == 'membership')
                        <img src="{{ $product['image'] }}">
                    @else
                        <i class="currency" style="background-size:162px 162px;width:162px;height:162px;"></i>
                    @endif
                </div>
                <div class="col-md-4 align-self-center">
                    <h1 style="font-weight:600;">{{ $product['display_name'] }}</h1>
                    <h3>${{ $product['price'] }}</h3>
                    <hr class="show-sm-only">
                </div>
                <div class="col-md-6 align-self-center">
                    <div class="mt-3 hide-sm"></div>
                    <form action="https://{{ (config('site.paypal_sandbox')) ? 'sandbox' : 'www' }}.paypal.com/cgi-bin/webscr" method="POST">
                        <input type="hidden" name="cmd" value="_donations">
                        <input type="hidden" name="image_url" value="{{ config('site.logo') }}">
                        <input type="hidden" name="business" value="{{ config('site.paypal_email') }}">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="no_note" value="1">
                        <input type="hidden" name="no_shipping" value="1">
                        <input type="hidden" name="amount" value="{{ $paypalProduct['price'] }}">
                        <input type="hidden" name="notify_url" value="{{ route('account.upgrade.notify') }}">
                        <input type="hidden" name="return" value="{{ route('account.upgrade.thank_you') }}">
                        <input type="hidden" name="cancel_return" value="{{ route('account.upgrade.canceled') }}">
                        <input type="hidden" name="item_name" value="{{ $paypalProduct['item_name'] }}">
                        <input type="hidden" name="item_number" value="{{ $paypalProduct['item_number'] }}">
                        <input type="hidden" name="lc" value="en_US">
                        <input type="hidden" name="rm" value="2">
                        <input type="hidden" name="cbt" value="Return to {{ config('site.name') }}">
                        <input type="hidden" name="custom" value="{{ Auth::user()->id }}">
                        <button class="btn btn-block btn-success mb-3" type="submit"><i class="fas fa-smile mr-1"></i> Purchase for Myself</button>
                    </form>
                    <button class="btn btn-block btn-success mb-2" disabled><i class="fas fa-gift mr-1"></i> Purchase as Gift</button>
                    <small class="text-muted">Payment is processed by PayPal.</small>
                </div>
            </div>
        </div>
    </div>
@endsection
