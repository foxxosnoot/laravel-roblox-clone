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
    'title' => "Edit \"{$item->name}\""
])

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Edit Item</h3>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('catalog.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <label for="name">Name</label>
                        <input class="form-control mb-2" type="text" name="name" placeholder="Item Name" value="{{ $item->name }}" required>
                        <label for="description">Description</label>
                        <textarea class="form-control mb-2" name="description" placeholder="Item Description" rows="5">{{ $item->description }}</textarea>
                        <label for="price">Price</label>
                        <input class="form-control mb-2" type="number" name="price" placeholder="Item Price" min="0" max="1000000" value="{{ $item->price }}">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="onsale" @if ($item->onsale()) checked @endif>
                            <label class="form-check-label" for="onsale">For Sale</label>
                        </div>
                        <button class="btn btn-block btn-success" type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Thumbnail</h3>
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $item->thumbnail() }}">
                </div>
            </div>
        </div>
    </div>
@endsection
