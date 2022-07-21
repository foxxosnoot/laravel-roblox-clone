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
    'title' => 'Character'
])

@section('meta')
    <meta name="item-types-with-padding" content="{{ json_encode(config('site.item_thumbnails_with_padding')) }}">
    <meta name="item-type-padding-amount" content="{{ itemTypePadding('default') }}">
    <meta
        name="routes"
        data-regen="{{ route('account.character.regenerate') }}"
        data-inventory="{{ route('account.character.inventory') }}"
        data-wearing="{{ route('account.character.wearing') }}"
        data-update="{{ route('account.character.update') }}"
    >
@endsection

@section('css')
    <style>
        .avatar-body-colors {
            max-width: 370px;
        }

        .avatar-body-color {
            border: 1.5px solid var(--section_border_color);
            border-radius: 5px;
            width: 50px;
            height: 50px;
            cursor: pointer;
            display: inline-block;
        }

        .avatar-item-category.active {
            font-weight: 600;
        }

        .avatar-body-part {
            border: 1.5px solid var(--section_border_color_inside);
            border-radius: 5px;
            outline: none;
            appearance: none;
            cursor: pointer;
        }

        .avatar-body-part:disabled {
            opacity: .8;
            pointer-events: none;
            cursor: not-allowed;
        }

        .angle-buttons .active {
            background: var(--section_bg_inside);
            border-radius: 0;
            box-shadow: var(--section_box_shadow)!important;
        }

        .palette {
            background: var(--section_bg_inside);
            border: 1px solid var(--section_inside_border_color);
            position: absolute;
            margin-left: 300px;
            margin-top: 308px;
            padding: 15px;
            z-index: 1337;
        }

        @media only screen and (max-width: 768px) {
            .avatar-body-colors {
                max-width: 320px;
            }

            .palette {
                margin-top: 200px;
                margin-left: 20px;
            }
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('js/character.js?v=9') }}"></script>
@endsection

@section('content')
<div class="palette" id="colors" style="display:none;">
    <div class="mb-2" id="colorsTitle" style="font-weight:600;"></div>
    <div class="avatar-body-colors">
        @foreach ($colors as $name => $hex)
            <div class="avatar-body-color" style="background:{{ $hex }};" title="{{ $name }}" data-color="{{ $name }}" data-toggle="tooltip"></div>
        @endforeach
    </div>
</div>
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col">
                    <h3>Avatar</h3>
                </div>
                <div class="col align-self-center text-right">
                    <button class="btn btn-sm btn-success" data-regenerate>Regenerate</button>
                </div>
            </div>
            <div class="card text-center">
                <div class="card-body">
                    <img id="character" src="{{ Auth::user()->thumbnail() }}" width="80%">
                    <div class="angle-buttons mt-1 text-right">
                        <button class="btn @if (Auth::user()->avatar()->angle == 'left') active @endif" data-angle="left">L</button>
                        <button class="btn @if (Auth::user()->avatar()->angle == 'right') active @endif" data-angle="right">R</button>
                    </div>
                </div>
            </div>
            <h3>Colors</h3>
            <div class="card text-center">
                <div class="card-body">
                    <div style="margin-bottom:2.5px;">
                        <button class="avatar-body-part" style="background-color:{{ Auth::user()->avatar()->color_head }};padding:25px;margin-top:-1px;" data-part="head"></button>
                    </div>
                    <div style="margin-bottom:2.5px;">
                        <button class="avatar-body-part" style="background-color:{{ Auth::user()->avatar()->color_left_arm }};padding:50px;padding-right:0px;" data-part="left_arm"></button>
                        <button class="avatar-body-part" style="background-color:{{ Auth::user()->avatar()->color_torso }};padding:50px;" data-part="torso"></button>
                        <button class="avatar-body-part" style="background-color:{{ Auth::user()->avatar()->color_right_arm }};padding:50px;padding-right:0px;" data-part="right_arm"></button>
                    </div>
                    <div>
                        <button class="avatar-body-part" style="background-color:{{ Auth::user()->avatar()->color_left_leg }};padding:50px;padding-right:0px;padding-left:47px;" data-part="left_leg"></button>
                        <button class="avatar-body-part" style="background-color:{{ Auth::user()->avatar()->color_right_leg }};padding:50px;padding-right:0px;padding-left:47px;" data-part="right_leg"></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h3>Inventory</h3>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-fill w mb-4" role="tablist">
                        @foreach (config('site.character_editor_item_types') as $type)
                            <li class="nav-item">
                                <span class="nav-link flex-sm-fill @if ($type == 'hat') active @endif" data-tab="{{ lcfirst(itemType($type, true)) }}">{{ itemType($type, true) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="row" id="inventory"></div>
                </div>
            </div>
            <h3>Currently Wearing</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row" id="wearing"></div>
                </div>
            </div>
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
