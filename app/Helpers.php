<?php
/**
 * MIT License
 *
 * Copyright (c) 2021-2022 FoxxoSnoot
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use App\Models\Item;
use App\Models\User;
use App\Models\Group;
use App\Models\Report;
use App\Models\StaffUser;
use Illuminate\Support\Str;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

require_once(__DIR__ . '/PaypalIPN.php');

function site_setting($key)
{
    $settings = SiteSettings::where('id', '=', 1)->first();

    return $settings->$key;
}

function staffUser()
{
    return User::where('id', '=', session('staff_user_site_id'))->first();
}

function pendingAssetsCount()
{
    if (Auth::user()->staff('can_review_pending_assets'))
        return Item::where('status', '=', 'pending')->count() + Group::where('is_thumbnail_pending', '=', true)->count();

    return 0;
}

function pendingReportsCount()
{
    if (Auth::user()->staff('can_review_pending_reports'))
        return Report::where('is_seen', '=', false)->count();

    return 0;
}

function itemType($type, $plural = false)
{
    $types = config('item_types');
    $type = (array_key_exists($type, $types)) ? $types[$type][($plural) ? 1 : 0] : ucfirst($type);

    return $type;
}

function itemTypeFromPlural($type)
{
    $types = config('item_types');

    foreach ($types as $t) {
        if ($t[1] == ucfirst($type))
            return $t[0];
    }

    return ucfirst($type);
}

function itemTypePadding($type)
{
    if ($type == 'default')
        return '5px';

    $types = config('site.item_thumbnails_with_padding');
    $padding = (in_array($type, $types)) ? 5 : 0;

    return "{$padding}px";
}

function render($id, $type)
{
    $url = config('site.renderer.url');
    $key = config('site.renderer.key');

    $response = Http::get("{$url}?seriousKey={$key}&type={$type}&id={$id}");

    return ($type != 'preview') ? $response->successful() : $response->json()['thumbnail'];
}
