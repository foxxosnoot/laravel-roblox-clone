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
use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function all(Request $request)
    {
        $json = [];
        $search = (isset($request->search)) ? trim($request->search) : '';

        if (Str::startsWith($search, '%'))
            $search = '';

        $users = User::where('username', 'LIKE', "%{$search}%")->get();

        $items = Item::where([
            ['name', 'LIKE', "%{$search}%"],
            ['public_view', '=', true],
            ['status', '=', 'approved']
        ])->get();

        $groups = Group::where([
            ['name', 'LIKE', "%{$search}%"],
            ['is_locked', '=', false]
        ])->get();

        foreach ($users as $user)
            $user->result_type = 'user';

        foreach ($items as $item)
            $item->result_type = 'item';

        foreach ($groups as $group)
            $group->result_type = 'group';

        $results = $users->merge($items)->merge($groups)->forPage($request->page, 9);

        if ($results->count() == 0 || !$search)
            return response()->json(['error' => 'No results found.']);

        foreach ($results as $result) {
            switch ($result->result_type) {
                case 'user':
                    $name = $result->username;
                    $image = $result->headshot();
                    $url = route('users.profile', $result->username);
                    break;
                case 'item':
                    $name = $result->name;
                    $image = $result->thumbnail();
                    $url = route('catalog.item', [$result->id, $result->slug()]);
                    break;
                case 'group':
                    $name = $result->name;
                    $image = $result->thumbnail();
                    $url = route('groups.view', [$result->id, $result->slug()]);
                    break;
            }

            $json[] = [
                'name' => $name,
                'image' => $image,
                'url' => $url
            ];
        }

        return response()->json($json);
    }
}
