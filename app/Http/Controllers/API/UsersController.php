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

namespace App\Http\Controllers\API;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function info(Request $request)
    {
        if ($request->has('username')) {
            $column = 'username';
            $value = $request->username;
        } else {
            $column = 'id';
            $value = $request->id;
        }

        $user = User::where($column, '=', $value);

        if (!$user->exists())
            return response()->json(['error' => 'Invalid user.']);

        $user = $user->first();

        return response()->json([
            'id'             => (int) $user->id,
            'username'       => (string) $user->username,
            'description'    => (string) $user->description,
            'has_membership' => (boolean) $user->hasMembership(),
            'is_staff'       => (boolean) $user->isStaff(),
            'created_at'     => (string) $user->created_at,
            'last_online'    => (string) $user->updated_at,
            'badges'         => (array) $user->badges(),
            'images' => [
                'thumbnail' => (string) $user->thumbnail(),
                'headshot'  => (string) $user->headshot()
            ]
        ]);
    }

    public function inventory(Request $request)
    {
        $user = User::where('id', '=', $request->id);
        $type = lcfirst(itemTypeFromPlural($request->category));

        if (!$user->exists())
            return response()->json(['error' => 'Invalid user.']);

        $user = $user->first();

        if (!in_array($type, config('site.inventory_item_types')))
            return response()->json(['error' => 'Invalid category.']);

        if (!$user->setting->public_inventory)
            return response()->json(['error' => "{$user->username} has made their inventory private."]);

        $items = Item::where([
            ['type', '=', $type],
            ['public_view', '=', true]
        ])->join('inventories', 'inventories.item_id', '=', 'items.id')->where('inventories.user_id', '=', $user->id)->orderBy('inventories.created_at', 'DESC')->paginate(8);
        $json = ['current_page' => $items->currentPage(), 'total_pages' => $items->lastPage(), 'items' => []];

        if ($items->count() == 0)
            return response()->json(['error' => "No {$request->category} found."]);

        foreach ($items as $item)
            $json['items'][] = ['id' => $item->item_id, 'name' => $item->name, 'type' => $item->type, 'thumbnail' => $item->thumbnail(), 'url' => route('catalog.item', [$item->item_id, $item->slug()])];

        return response()->json($json);
    }
}
