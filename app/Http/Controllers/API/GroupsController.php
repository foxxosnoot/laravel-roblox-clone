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
use App\Models\GroupRank;
use App\Models\GroupWall;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GroupsController extends Controller
{
    public function members(Request $request)
    {
        $json = [];
        $group = Group::where('id', '=', $request->id);

        if (!$group->exists())
            return response()->json(['error' => 'This group does not exist.']);

        $group = $group->first();
        $members = GroupMember::where([
            ['group_id', '=', $group->id],
            ['rank', '=', $request->rank]
        ]);

        if ($members->count() == 0)
            return response()->json(['error' => 'No members found.']);

        $members = $members->orderBy('updated_at', 'DESC')->paginate(8);

        foreach ($members as $member)
            $json[] = [
                'id'        => (int)    $member->user->id,
                'username'  => (string) $member->user->username,
                'thumbnail' => (string) $member->user->thumbnail(),
                'url'       => (string) route('users.profile', $member->user->username)
            ];

        return response()->json(['current_page' => $members->currentPage(), 'total_pages' => $members->lastPage(), 'members' => $json]);
    }

    public function items(Request $request)
    {
        $json = [];
        $group = Group::where('id', '=', $request->id);

        if (!$group->exists())
            return response()->json(['error' => 'This group does not exist.']);

        $group = $group->first();
        $items = Item::where([
            ['creator_id', '=', $group->id],
            ['creator_type', '=', 'group'],
            ['status', '=', 'approved'],
            ['public_view', '=', true]
        ]);

        if ($items->count() == 0)
            return response()->json(['error' => 'No items found.']);

        $items = $items->orderBy('updated_at', 'DESC')->paginate(6);

        foreach ($items as $item)
            $json[] = [
                'name'      => (string) $item->name,
                'thumbnail' => (string) $item->thumbnail(),
                'price'     => (integer) $item->price,
                'onsale'    => (boolean) $item->onsale(),
                'url'       => (string) route('catalog.item', [$item->id, $item->slug()])
            ];

        return response()->json(['current_page' => $items->currentPage(), 'total_pages' => $items->lastPage(), 'items' => $json]);
    }

    public function wall(Request $request)
    {
        $json = [];
        $group = Group::where('id', '=', $request->id);

        if (!$group->exists())
            return response()->json(['error' => 'This group does not exist.']);

        $group = $group->first();
        $wallPosts = GroupWall::where('group_id', '=', $group->id);

        if ($wallPosts->count() == 0)
            return response()->json(['error' => 'No posts found.']);

        $wallPosts = $wallPosts->orderBy('is_pinned', 'DESC')->orderBy('created_at', 'DESC')->paginate(10);

        foreach ($wallPosts as $wallPost)
            $json[] = [
                'body'      => (string) $wallPost->body,
                'time_ago'  => (string) $wallPost->created_at->diffForHumans(),
                'is_pinned' => (boolean) $wallPost->is_pinned,
                'creator'   => [
                    'username'  => (string) $wallPost->user->username,
                    'thumbnail' => (string) $wallPost->user->thumbnail(),
                    'url'       => (string) route('users.profile', $wallPost->user->username)
                ]
            ];

        return response()->json(['current_page' => $wallPosts->currentPage(), 'total_pages' => $wallPosts->lastPage(), 'posts' => $json]);
    }

    public function wallPost(Request $request)
    {
        $body = trim($request->body ?? '');
        $group = Group::where('id', '=', $request->id);

        if (!$group->exists())
            return response()->json(['error' => 'This group does not exist.']);

        $group = $group->first();

        if (!Auth::user()->isInGroup($group->id))
            return response()->json(['error' => 'You are not a member of this group.']);

        if (strlen($body) < 3 || strlen($body) > 150)
            return response()->json(['error' => 'Wall post may not be greater than 150 characters and must be at least 3 characters long.']);

        $wallPost = new GroupWall;
        $wallPost->user_id = Auth::user()->id;
        $wallPost->group_id = $group->id;
        $wallPost->body = $request->body;
        $wallPost->save();

        return response()->json(['success' => 'Wall post has been created.']);
    }

    public function kickMember(Request $request)
    {
        $group = Group::where('id', '=', $request->id);
        $member = GroupMember::where([
            ['user_id', '=', $request->userId],
            ['group_id', '=', $request->id]
        ]);

        if (!$group->exists())
            return response()->json(['error' => 'This group does not exist.']);

        if (!$member->exists())
            return response()->json(['error' => 'This user is not in this group.']);

        $group = $group->first();
        $member = $member->first();

        if (Auth::user()->id != $group->owner->id || $member->rank == 255)
            return response()->json(['error' => 'You can not do this.']);

        $member->delete();

        return response()->json(['success' => 'User has been kicked.']);
    }

    public function rankMember(Request $request)
    {
        $group = Group::where('id', '=', $request->id);
        $member = GroupMember::where([
            ['user_id', '=', $request->userId],
            ['group_id', '=', $request->id]
        ]);
        $rank = GroupRank::where([
            ['group_id', '=', $request->id],
            ['rank', '=', $request->rank]
        ]);

        if (!$group->exists())
            return response()->json(['error' => 'This group does not exist.']);

        if (!$member->exists())
            return response()->json(['error' => 'This user is not in this group.']);

        if (!$rank->exists())
            return response()->json(['error' => 'This rank does not exist.']);

        $group = $group->first();
        $member = $member->first();
        $rank = $rank->first();

        if (Auth::user()->id != $group->owner->id || $rank->rank == 255)
            return response()->json(['error' => 'You can not do this.']);

        $member->rank = $rank->rank;
        $member->save();

        return response()->json(['success' => 'User has been ranked.']);
    }

    public function payout(Request $request)
    {
        $group = Group::where('id', '=', $request->id);
        $user = User::where('username', '=', $request->username);

        if (!$group->exists())
            return response()->json(['error' => 'This group does not exist.']);

        if (!$user->exists())
            return response()->json(['error' => 'This user does not exist.']);

        if (!$request->has('amount') || !is_numeric($request->amount) || $request->amount == 0)
            return response()->json(['error' => 'Invalid amount.']);

        $group = $group->first();
        $user = $user->first();

        if (!$user->isInGroup($group->id))
            return response()->json(['error' => 'This user is not in this group.']);

        if ($group->vault < $request->amount)
            return response()->json(['error' => 'Vault does not have enough currency.']);

        $group->vault -= $request->amount;
        $group->save();

        $user->timestamps = false;
        $user->currency += $request->amount;
        $user->save();

        return response()->json(['success' => 'User has been paid out.', 'vault' => number_format($group->vault)]);
    }
}
