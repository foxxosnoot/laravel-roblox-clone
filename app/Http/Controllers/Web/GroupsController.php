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

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GroupJoinRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GroupsController extends Controller
{
    public function index(Request $request)
    {
        $search = (isset($request->search)) ? trim($request->search) : '';
        $groups = Group::where([
            ['name', 'LIKE', "%{$search}%"],
            ['is_locked', '=', false]
        ])->join('group_members', 'group_members.group_id', '=', 'groups.id')->select('groups.*', DB::raw('COUNT(group_members.id) AS member_count'))->groupBy([
            'groups.id',
            'groups.owner_id',
            'groups.name',
            'groups.description',
            'groups.vault',
            'groups.is_private',
            'groups.is_vault_viewable',
            'groups.is_locked',
            'groups.thumbnail_url',
            'groups.is_thumbnail_pending',
            'groups.created_at',
            'groups.updated_at'
        ])->orderBy('member_count', 'DESC')->paginate(12);

        return view('web.groups.index')->with([
            'search' => $search,
            'groups' => $groups
        ]);
    }

    public function view($id, $slug)
    {
        $group = Group::where('id', '=', $id)->firstOrFail();

        if ($slug != $group->slug()) abort(404);

        $isPending = (Auth::check()) ? GroupJoinRequest::where([
            ['user_id', '=', Auth::user()->id],
            ['group_id', '=', $group->id]
        ])->exists() : false;

        return view('web.groups.view')->with([
            'group' => $group,
            'isPending' => $isPending
        ]);
    }

    public function manage($id, $slug)
    {
        $group = Group::where('id', '=', $id)->firstOrFail();
        $joinRequests = ($group->is_private) ? GroupJoinRequest::where('group_id', '=', $group->id)->get() : [];

        if ($slug != $group->slug() || Auth::user()->id != $group->owner->id) abort(404);

        return view('web.groups.manage')->with([
            'group' => $group,
            'joinRequests' => $joinRequests
        ]);
    }

    public function update(Request $request)
    {
        $group = Group::where('id', '=', $request->id)->firstOrFail();
        $fromSettingsTab = $request->has('from_settings_tab');

        if (Auth::user()->id != $group->owner->id) abort(404);

        if ($fromSettingsTab) {
            $group->is_private = $request->has('is_private');
            $group->is_vault_viewable = $request->has('is_vault_viewable');
            $group->save();

            if (!$group->is_private)
                GroupJoinRequest::where('group_id', '=', $group->id)->delete();
        } else {
            $filename = Str::random(50);
            $validate = [
                'description' => ['max:1024']
            ];

            if ($request->hasFile('logo'))
                $validate['logo'] = ['dimensions:min_width=100,min_height=100', 'mimes:png,jpg,jpeg', 'max:2048'];

            $this->validate($request, $validate);

            $group->description = $request->description;

            if ($request->hasFile('logo')) {
                if ($group->is_thumbnail_pending)
                    return back()->withErrors(['Current logo hasn\'t been moderated yet.']);

                $group->is_thumbnail_pending = true;

                if (Storage::exists("thumbnails/{$group->thumbnail_url}.png"))
                    Storage::delete("thumbnails/{$group->thumbnail_url}.png");

                $group->thumbnail_url = $filename;

                $logo = imagecreatefromstring($request->file('logo')->get());
                $img = imagecreatetruecolor(420, 420);

                imagealphablending($img, false);
                imagesavealpha($img, true);
                imagefilledrectangle($img, 0, 0, 420, 420, imagecolorallocatealpha($img, 255, 255, 255, 127));
                imagecopyresampled($img, $logo, 0, 0, 0, 0, 420, 420, imagesx($logo), imagesy($logo));

                $logo = $img;
                imagealphablending($logo, false);
                imagesavealpha($logo, true);

                Storage::put("thumbnails/{$filename}.png", Image::make($logo)->encode('png'));
            }

            $group->save();

        }

        return back()->with('success_message', 'Group has been updated.');
    }

    public function updateJoinRequest(Request $request)
    {
        $joinRequest = GroupJoinRequest::where('id', '=', $request->id)->firstOrFail();

        if (Auth::user()->id != $joinRequest->group->owner->id) abort(404);

        switch ($request->action) {
            case 'accept':
                $joinRequest->delete();

                $groupMember = new GroupMember;
                $groupMember->user_id = $joinRequest->user->id;
                $groupMember->group_id = $joinRequest->group->id;
                $groupMember->rank = 1;
                $groupMember->save();

                return back()->with('success_message', 'Join request has been accepted.');
            case 'decline':
                $joinRequest->delete();

                return back()->with('success_message', 'Join request has been declined.');
            default:
                abort(404);
        }
    }

    public function membership(Request $request)
    {
        $group = Group::where('id', '=', $request->id)->firstOrFail();
        $isInGroup = Auth::user()->isInGroup($group->id);
        $message = (!$isInGroup) ? 'You have joined this group.' : 'You have left this group.';

        if (Auth::user()->id == $group->owner->id) abort(404);

        if (!$isInGroup) {
            if (Auth::user()->reachedGroupLimit())
                return back()->withErrors(['You have reached the limit of groups you can be apart of.']);

            if ($group->is_private) {
                $groupJoinRequest = new GroupJoinRequest;
                $groupJoinRequest->user_id = Auth::user()->id;
                $groupJoinRequest->group_id = $group->id;
                $groupJoinRequest->save();

                $message = 'Join request has been sent.';
            } else {
                $groupMember = new GroupMember;
                $groupMember->user_id = Auth::user()->id;
                $groupMember->group_id = $group->id;
                $groupMember->rank = 1;
                $groupMember->save();
            }
        } else {
            if (Auth::user()->primary_group_id == $group->id) {
                $user = Auth::user();
                $user->primary_group_id = null;
                $user->save();
            }

            $groupMember = GroupMember::where([
                ['user_id', '=', Auth::user()->id],
                ['group_id', '=', $group->id]
            ])->first();
            $groupMember->delete();
        }

        return back()->with('success_message', $message);
    }

    public function setPrimary(Request $request)
    {
        $group = Group::where('id', '=', $request->id)->firstOrFail();
        $isInGroup = Auth::user()->isInGroup($group->id);
        $user = Auth::user();

        if ($group->id == Auth::user()->primary_group_id) {
            $user->primary_group_id = null;
            $user->save();

            return back()->with('success_message', 'This is no longer your primary group.');
        }

        if (!$isInGroup)
            return back()->withErrors(['You are not a member of this group.']);

        $user->primary_group_id = $group->id;
        $user->save();

        return back()->with('success_message', 'This is now your primary group.');
    }
}
