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
    'title' => $item->name,
    'image' => $item->thumbnail()
])

@section('meta')
    <meta
        name="item-info"
        data-id="{{ $item->id }}"

        @if ($item->isTimed())
            data-onsale-until="{{ $item->onsale_until->format('Y-m-d H:i') }}"
        @endif
    >
@endsection

@section('css')
    <style>
        img.creator {
            background: var(--headshot_bg);
            border-radius: 50%;
            width: 80%;
        }

        .reseller:not(:first-child) {
            padding-top: 15px;
        }

        .reseller:not(:last-child) {
            padding-bottom: 15px;
            border-bottom: 1px solid var(--divider_color);
        }
    </style>
@endsection

@section('js')
    @if ($item->isTimed())
        <script src="{{ asset('js/vendor/jquery.countdown.min.js') }}"></script>
        <script src="{{ asset('js/vendor/moment.min.js') }}"></script>
        <script src="{{ asset('js/vendor/moment.timezone.min.js') }}"></script>
        <script src="{{ asset('js/timed_item.js') }}"></script>
    @endif
@endsection

@section('content')
    @if (!$item->public_view)
        <div class="alert bg-warning"><i class="fas fa-exclamation-triangle"></i> This item is not public.</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    @if (Auth::check() && Auth::user()->ownsItem($item->id))
                        <div class="bg-success text-white text-center" id="ownershipCheck" style="border-radius:50%;width:30px;height:30px;position:absolute;cursor:pointer;margin-left:5px;margin-top:5px;" title="You own this item" data-toggle="tooltip">
                            <i class="fas fa-check" style="font-size:18px;margin-top:7px;"></i>
                        </div>
                    @endif

                    <img class="thumbnail" id="thumbnail" style="background:var(--section_bg_inside);border-radius:6px;padding:{{ itemTypePadding($item->type) }};" src="{{ $item->thumbnail() }}">

                    @if (Auth::check() && ($item->creator_type == 'group' || ($item->creator_type == 'user' && $item->creator->id != Auth::user()->id && !$item->creator->isStaff())))
                        <div class="text-center mt-2 hide-sm">
                            <a href="{{ route('report.index', ['item', $item->id]) }}" class="text-danger">
                                <i class="fas fa-flag"></i>
                                <span>Report</span>
                            </a>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 style="font-weight:600;">{{ $item->name }}</h4>
                    <div class="text-truncate show-sm-only" style="margin-top:-5px;margin-bottom:5px;">
                        <span>Created by</span>
                        <a href="{{ $item->creatorUrl() }}">{{ $item->creatorName() }}</a>
                        @if ($item->creator_type == 'user' && $item->creator->is_verified)
                            <i class="fas fa-shield-check text-success ml-1" style="font-size:13px;" title="This user is verified." data-toggle="tooltip"></i>
                        @endif
                    </div>
                    <div style="max-height:175px;overflow-y:auto;">{!! (!empty($item->description)) ? nl2br(e($item->description)) : '<div class="text-muted">This item does not have a description.</div>' !!}</div>
                </div>
                <div class="col-md-3 text-center">
                    <a href="{{ $item->creatorUrl() }}" class="hide-sm">
                        <img class="creator" src="{{ $item->creatorImage() }}">
                        <div class="text-truncate mt-1">
                            <span>{{ $item->creatorName() }}</span>
                            @if ($item->creator_type == 'user' && $item->creator->is_verified)
                                <i class="fas fa-shield-check text-success ml-1" style="font-size:13px;" title="This user is verified." data-toggle="tooltip"></i>
                            @endif
                        </div>
                    </a>

                    @if ($item->isTimed())
                        <div class="text-danger mt-2" id="timer"></div>
                    @endif

                    @if ($item->limited)
                        <div class="text-danger mt-2">{{ ($item->stock > 0) ? "{$item->stock} LEFT" : 'SOLD OUT' }}</div>
                    @endif

                    @auth
                        @if (site_setting('catalog_purchases_enabled') && $item->status == 'approved' && $item->onsale())
                            @if (!Auth::user()->ownsItem($item->id))
                                <button class="btn btn-block btn-sm btn-success mt-3" data-toggle="modal" data-target="#purchaseConfirmation">{!! ($item->price == 0) ? 'Take for Free' : 'Buy for &nbsp;<i class="currency text-white"></i> ' . number_format($item->price) !!}</button>
                            @else
                                <button class="btn btn-block btn-sm btn-success mt-3" disabled>{!! ($item->price == 0) ? 'Take for Free' : 'Buy for &nbsp;<i class="currency text-white"></i> ' . number_format($item->price) !!}</button>
                            @endif
                        @endif

                        @if (Auth::user()->canEditItem($item->id))
                            <a href="{{ route('catalog.edit', [$item->id, $item->slug()]) }}" class="btn btn-block btn-sm btn-primary mt-3"><i class="fas fa-edit"></i> Edit</a>
                        @endif

                        @if (Auth::user()->isStaff() && Auth::user()->staff('can_view_item_info'))
                            <a href="{{ route('admin.items.view', $item->id) }}" class="btn btn-block btn-sm btn-danger mt-3" target="_blank"><i class="fas fa-gavel"></i> View in Panel</a>
                        @endif

                        @if (Auth::user()->isStaff() && Auth::user()->staff('can_edit_item_info') && !in_array($item->type, ['tshirt', 'shirt', 'pants']))
                            <a href="{{ route('admin.edit_item.index', $item->id) }}" class="btn btn-block btn-sm btn-danger mt-3" target="_blank"><i class="fas fa-gavel"></i> Edit in Panel</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row text-center">
                <div class="col-6 col-md">
                    <h5>{{ $item->created_at->format('M d, Y') }}</h5>
                    <h6 class="text-muted" style="margin-top:-10px;">TIME CREATED</h6>
                </div>
                <div class="col-6 col-md">
                    <h5>{{ $item->updated_at->format('M d, Y') }}</h5>
                    <h6 class="text-muted" style="margin-top:-10px;">LAST UPDATED</h6>
                </div>
                <div class="col-6 col-md">
                    <h5>{{ number_format($item->owners()->count()) }}</h5>
                    <h6 class="text-muted" style="margin-top:-10px;">OWNERS</h6>
                </div>
                <div class="col-6 col-md">
                    <h5>{{ itemType($item->type) }}</h5>
                    <h6 class="text-muted" style="margin-top:-10px;">TYPE</h6>
                </div>
            </div>
            @if (Auth::check() && ($item->creator_type == 'group' || ($item->creator_type == 'user' && $item->creator->id != Auth::user()->id && !$item->creator->isStaff())))
                <div class="text-center mt-2 mt-2 show-sm-only">
                    <a href="{{ route('report.index', ['item', $item->id]) }}" class="text-danger">
                        <i class="fas fa-flag"></i>
                        <span>Report</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
    <h3>Suggested Items</h3>
    <div class="card">
        <div class="card-body">
            <div class="row">
                @forelse ($suggestions as $suggestion)
                    <div class="col-6 col-md-2">
                        <div class="card mb-sm-only" style="border:none;">
                            <div class="card-body" style="padding:0;">
                                <a href="{{ route('catalog.item', [$suggestion->id, $suggestion->slug()]) }}" style="color:inherit;font-weight:600;">
                                    @if ($suggestion->limited)
                                        <div class="bg-primary text-white text-center" style="border-radius:50%;width:30px;height:30px;position:absolute;margin-left:5px;margin-top:5px;">
                                            <span style="font-size:20px;font-weight:600;margin-top:7px;">C</span>
                                        </div>
                                    @endif
                                    <img style="background:var(--section_bg_inside);border-radius:6px;padding:{{ itemTypePadding($suggestion->type) }};" src="{{ $suggestion->thumbnail() }}">
                                    <div class="text-truncate mt-1">{{ $suggestion->name }}</div>
                                </a>

                                @if ($suggestion->onsale() && $suggestion->price == 0)
                                    <span class="text-success">Free</span>
                                @elseif (!$suggestion->onsale())
                                    <span class="text-muted">Off Sale</span>
                                @else
                                    <span><i class="currency"></i> {{ number_format($suggestion->price) }}</span>
                                @endif

                                @if ($suggestion->limited)
                                    <div class="float-right text-danger">{{ ($suggestion->stock > 0) ? "{$suggestion->stock} LEFT" : 'SOLD OUT' }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col">No items found.</div>
                @endforelse
            </div>
        </div>
    </div>
    @if ($item->limited && $item->stock <= 0)
        <div class="row">
            <div class="col">
                <h3>Resellers</h3>
            </div>
            @if (Auth::check() && Auth::user()->ownsItem($item->id) && !empty(Auth::user()->resellableCopiesOfItem($item->id)))
                <div class="col text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#resell">Sell</button>
                </div>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                @forelse ($item->resellers() as $listing)
                    <div class="row reseller">
                        <div class="col-5 col-md-2 text-center">
                            <a href="{{ route('users.profile', $listing->seller->username) }}">
                                <img class="creator" src="{{ $listing->seller->headshot() }}">
                            </a>
                        </div>
                        <div class="col-7 col-md-10 align-self-center">
                            <p class="text-truncate">
                                <a href="{{ route('users.profile', $listing->seller->username) }}" style="font-size:23px;">{{ $listing->seller->username }}</a>
                                @if ($listing->seller->is_verified)
                                    <i class="fas fa-shield-check text-success ml-1" style="font-size:16px;" title="This user is verified." data-toggle="tooltip"></i>
                                @endif
                            </p>

                            @if (!Auth::check() || Auth::user()->id != $listing->seller->id)
                                <button class="btn btn-success" data-toggle="modal" data-target="#resellPurchaseConfirmation_{{ $listing->id }}" @if (!Auth::check()) disabled @endif>Buy for <i class="currency"></i> {{ number_format($listing->price) }}</button>
                            @else
                                <form action="{{ route('catalog.take_off_sale') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $listing->id }}">
                                    <button class="btn btn-danger" type="submit">Selling for <i class="currency"></i> {{ number_format($listing->price) }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p>No one is currently reselling this item.</p>
                @endforelse
            </div>
        </div>
        {{ $item->resellers()->onEachSide(1) }}
    @endif

    @if (Auth::check())
        @if ($item->onsale() && $item->status == 'approved' && (!Auth::user()->ownsItem($item->id)))
            <div class="modal fade" id="purchaseConfirmation" tabindex="-1" role="dialog">
                <form action="{{ route('catalog.purchase') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Purchase Item</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure that you want to {{ ($item->price == 0) ? 'take' : 'purchase' }} this {{ strtolower(itemType($item->type)) }} for {!! ($item->price == 0) ? '<span class="text-primary">Free</span>' : '<i class="currency"></i> ' . number_format($item->price) !!}?</p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success" type="submit">Yes</button>
                                <button class="btn btn-danger" data-dismiss="modal">No</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        @if ($item->limited && $item->stock <= 0)
            @if (Auth::user()->ownsItem($item->id) && !empty(Auth::user()->resellableCopiesOfItem($item->id)))
                <div class="modal fade" id="resell" tabindex="-1" role="dialog">
                    <form action="{{ route('catalog.resell') }}" method="POST">
                        @csrf
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Sell Item</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Please select the copy you wish to sell.</p>
                                    <select class="form-control mb-2" name="id">
                                        @foreach (Auth::user()->resellableCopiesOfItem($item->id) as $copy)
                                            <option value="{{ $copy->id }}">Copy #{{ $copy->number }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control" type="number" name="price" placeholder="Price" min="1" max="1000000">
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success" type="submit">Sell</button>
                                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            @foreach ($item->resellers() as $listing)
                <div class="modal fade" id="resellPurchaseConfirmation_{{ $listing->id }}" tabindex="-1" role="dialog">
                    <form action="{{ route('catalog.purchase') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <input type="hidden" name="reseller_id" value="{{ $listing->id }}">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Purchase Item</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure that you want to purchase this {{ strtolower(itemType($item->type)) }} for <i class="currency"></i> {{ number_format($listing->price) }}?</p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success" type="submit">Yes</button>
                                    <button class="btn btn-danger" data-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        @endif
    @endif
@endsection
