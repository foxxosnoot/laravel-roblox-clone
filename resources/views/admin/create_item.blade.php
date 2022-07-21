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
    'title' => $title
])

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.create_item.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="row">
                    <div class="col-md-6">
                        <label for="name">Name</label>
                        <input class="form-control mb-2" type="text" name="name" placeholder="Item Name">
                    </div>
                    <div class="col-md-6">
                        <label for="price">Price</label>
                        <input class="form-control mb-2" type="number" name="price" placeholder="Item Price" min="0" max="1000000">
                    </div>
                </div>
                <label for="description">Description</label>
                <textarea class="form-control mb-2" name="description" placeholder="Item Description" rows="5"></textarea>
                <label for="stock">Stock</label>
                <input class="form-control mb-2" type="number" name="stock" placeholder="Limited Stock" min="0" max="500">
                <label for="onsale_for">Onsale For</label>
                <select class="form-control mb-2" name="onsale_for">
                    <option value="forever" selected>Forever</option>
                    <option value="1_hour">1 Hour</option>
                    <option value="12_hours">12 Hours</option>
                    <option value="1_day">1 Day</option>
                    <option value="3_days">3 Days</option>
                    <option value="7_days">7 Days</option>
                    <option value="14_days">14 Days</option>
                    <option value="21_days">21 Days</option>
                    <option value="1_month">1 Month</option>
                </select>
                <div class="row mb-1">
                    <div class="col-md-6">
                        <label for="image">Image</label><br>
                        <input class="mb-3" name="image" type="file">
                    </div>

                    @if ($type != 'face')
                        <div class="col-md-6">
                            <label for="model">Model</label><br>
                            <input class="mb-3" name="model" type="file">
                        </div>
                    @endif
                </div>
                <label>Options</label>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="onsale">
                            <label class="form-check-label" for="onsale">For Sale</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="limited">
                            <label class="form-check-label" for="limited">Limited</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="public">
                            <label class="form-check-label" for="public_view">Public</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="official">
                            <label class="form-check-label" for="official">Official (upload to system)</label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-block btn-success" type="submit">Create</button>
            </form>
        </div>
    </div>
@endsection
