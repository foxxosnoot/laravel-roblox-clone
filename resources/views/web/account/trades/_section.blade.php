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

<div class="col-md-6">
    <h3>{{ $title }}</h3>
    <div class="card">
        <div class="card-body" style="padding-bottom:0;">
            <div class="row">
                @foreach ($items as $item)
                    <div class="col-6 col-md-4">
                        <a href="{{ route('catalog.item', [$item['id'], $item['slug']]) }}" style="color:inherit;text-decoration:none;" target="_blank">
                            <div class="card" style="border:none;">
                                <img style="background:var(--section_bg_inside);border-radius:6px;padding:{{ itemTypePadding($item->type) }};" src="{{ $item['thumbnail'] }}">
                                <div class="text-truncate mt-1"><strong>{{ $item['name'] }}</strong></div>
                            </div>
                        </a>
                    </div>
                @endforeach

                @if ($currency)
                    <div class="col-md-12">
                        <span>+ <i class="currency"></i> {{ number_format($currency) }}</span>
                        <div class="mb-3"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
