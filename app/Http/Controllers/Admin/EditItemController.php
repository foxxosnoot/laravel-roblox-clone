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

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Jobs\RenderUser;
use App\Models\UserAvatar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EditItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next) {
            if (!staffUser()->staff('can_edit_item_info')) abort(404);

            return $next($request);
        });
    }

    public function index($id)
    {
        $item = Item::where('id', '=', $id)->firstOrFail();

        if (in_array($item->type, ['tshirt', 'shirt', 'pants'])) abort(404);

        return view('admin.edit_item')->with([
            'item' => $item
        ]);
    }

    public function update(Request $request)
    {
        $item = Item::where('id', '=', $request->id)->firstOrFail();
        $onsale = $request->has('onsale');
        $public = $request->has('public');
        $limited = $request->has('limited');

        if (in_array($item->type, ['tshirt', 'shirt', 'pants'])) abort(404);

        $validate = [
            'name' => ['required', 'min:3', 'max:70'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'description' => ['max:1024']
        ];

        $validate['image'] = ['mimes:png,jpg,jpeg', 'max:2048'];

        if ($item->type != 'face')
            $validate['model'] = ['mimes:txt', 'max:2048'];

        if ($onsale)
            $validate['price'] = ['required', 'numeric', 'min:0', 'max:1000000'];

        $this->validate($request, $validate);

        switch ($request->onsale_for) {
            case '1_hour':
                $time = 3600;
                break;
            case '12_hours':
                $time = 43200;
                break;
            case '1_day':
                $time = 86400;
                break;
            case '3_days':
                $time = 259200;
                break;
            case '7_days':
                $time = 604800;
                break;
            case '14_days':
                $time = 1209600;
                break;
            case '21_days':
                $time = 1814400;
                break;
            case '1_month':
                $time = 2592000;
                break;
        }

        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->limited = $limited;
        $item->stock = ($limited) ? $request->stock : 0;
        $item->public_view = $public;
        $item->onsale = $onsale;

        if ($request->has('onsale_for'))
            $item->onsale_until = ($onsale && isset($time)) ? Carbon::createFromTimestamp(time() + $time)->format('Y-m-d H:i:s') : null;

        $item->save();

        if ($request->hasFile('image'))
            Storage::putFileAs('uploads', $request->file('image'), "{$item->filename}.png");

        if ($item->type != 'face' && $request->hasFile('model'))
            Storage::putFileAs('uploads', $request->file('model'), "{$item->filename}.obj");

        if ($request->hasFile('image') || $request->hasFile('model')) {
            render($item->id, 'item');

            if (in_array($item->type, ['hat', 'head', 'face', 'gadget', 'tshirt', 'shirt', 'pants'])) {
                $avatars = UserAvatar::where(($item->type == 'hat') ? 'hat_1' : $item->type, '=', $item->id)->orWhere('hat_2', '=', $item->id)->orWhere('hat_3', '=', $item->id)->get();

                foreach ($avatars as $avatar) {
                    $user = User::where('id', '=', $avatar->user_id)->first();

                    if ($user->isWearingItem($item->id))
                        RenderUser::dispatch($user->id);
                }
            }
        }

        return back()->with('success_message', 'Item has been updated.');
    }
}
