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

namespace App\Http\Controllers\Web;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\Friend;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UsernameHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $search = (isset($request->search)) ? trim($request->search) : '';
        $button = [];
        $where = [
            ['username', 'LIKE', "%{$search}%"]
        ];

        switch ($request->category) {
            case '':
            case 'all':
                $category = 'all';
                $button['text'] = 'Online';
                $button['category'] = 'online';
                break;
            case 'online':
                $where[] = ['updated_at', '>=', Carbon::now()->subMinutes(3)];
                $category = 'online';
                $button['text'] = 'All';
                $button['category'] = 'all';
                break;
            default:
                abort(404);
        }

        $users = User::where($where)->orderBy('created_at', 'ASC')->paginate(25);

        return view('web.users.index')->with([
            'category' => $category,
            'button' => $button,
            'users' => $users
        ]);
    }

    public function profile($username)
    {
        $user = User::where('username', '=', $username);

        if (!$user->exists()) {
            $usernameHistory = UsernameHistory::where('username', '=', $username);

            if (!$usernameHistory->exists()) abort(404);

            return redirect()->route('users.profile', $usernameHistory->first()->user->username);
        }

        $user = $user->first();
        $friends = $user->friends()->take(6);
        $groups = $user->groups()->take(6);

        if (Auth::check()) {
            $friendsArray = [];

            foreach ($user->friends() as $friend)
                $friendsArray[] = $friend->id;

            $areFriends = in_array(Auth::user()->id, $friendsArray);
            $isPending = Friend::where('is_pending', '=', true)->where(function($query) use($user) {
                $query->where([
                    ['receiver_id', '=', $user->id],
                    ['sender_id', '=', Auth::user()->id]
                ])->orWhere([
                    ['receiver_id', '=', Auth::user()->id],
                    ['sender_id', '=', $user->id]
                ]);
            })->first();
        }

        return view('web.users.profile')->with([
            'user' => $user,
            'friends' => $friends,
            'groups' => $groups,
            'areFriends' => $areFriends ?? false,
            'isPending' => $isPending ?? false
        ]);
    }

    public function friends($username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $friendsArray = [];

        foreach ($user->friends() as $friend)
            $friendsArray[] = $friend->id;

        $friends = User::whereIn('id', $friendsArray)->paginate(24);

        return view('web.users.friends')->with([
            'user' => $user,
            'friends' => $friends
        ]);
    }

    public function groups($username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $groupsArray = [];

        foreach ($user->groups() as $group)
            $groupsArray[] = $group->id;

        $groups = Group::whereIn('id', $groupsArray)->paginate(24);

        return view('web.users.groups')->with([
            'user' => $user,
            'groups' => $groups
        ]);
    }
}
