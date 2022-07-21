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
    'title' => "Item: {$item->name}"
])

@section('content')
    <div class="row">
        <div class="col-md-3">
            <h3>Thumbnail</h3>
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $item->thumbnail() }}">
                    <a href="{{ route('catalog.item', [$item->id, $item->slug()]) }}" class="btn btn-block btn-primary mt-2" target="_blank"><i class="fas fa-link"></i> View in Catalog</a>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <h3>Item Info</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6"><strong>Creation Date:</strong></div>
                        <div class="col-6 text-right">{{ $item->created_at->format('M d, Y') }}</div>
                        <div class="col-6"><strong>Last Updated:</strong></div>
                        <div class="col-6 text-right">{{ $item->updated_at->format('M d, Y') }}</div>
                        <div class="col-6"><strong>Owners:</strong></div>
                        <div class="col-6 text-right">{{ number_format($item->owners()->count()) }}</div>
                        <div class="col-6"><strong>Price</strong></div>
                        <div class="col-6 text-right">{!! ($item->status == 'approved') ? '<i class="currency"></i> ' . number_format($item->price) : 'Off Sale' !!}</div>
                        <div class="col-6"><strong>Is Public</strong></div>
                        <div class="col-6 text-right">{{ ($item->public_view) ? 'Yes' : 'No' }}</div>
                        <div class="col-6"><strong>Status</strong></div>
                        <div class="col-6 text-right">{{ ucfirst($item->status) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <form action="{{ route('admin.items.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $item->id }}">
                <h3>Actions</h3>
                <div class="card">
                    <div class="card-body">
                        @if (staffUser()->staff('can_review_pending_assets'))
                            <button class="btn btn-block btn-outline-{{ ($item->status != 'approved') ? 'primary' : 'danger' }} mb-2" name="action" value="status">
                                @if ($item->status != 'approved')
                                    <i class="fas fa-check mr-1"></i>
                                    <span>Approve</span>
                                @else
                                    <i class="fas fa-times mr-1"></i>
                                    <span>Deny</span>
                                @endif
                            </button>
                        @endif

                        @if ((staffUser()->staff('can_edit_item_info') || $item->creator->id == staffUser()->id) && !in_array($item->type, ['tshirt', 'shirt', 'pants']))
                            <a href="{{ route('admin.edit_item.index', $item->id) }}" class="btn btn-block btn-outline-dark mb-2">
                                <i class="fas fa-edit mr-1"></i>
                                <span>Edit</span>
                            </a>
                        @endif

                        @if (staffUser()->staff('can_render_thumbnails'))
                            <button class="btn btn-block btn-outline-dark mb-2" name="action" value="regen">
                                <i class="fas fa-sync mr-1"></i>
                                <span>Regen Thumbnail</span>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
