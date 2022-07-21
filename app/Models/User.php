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

namespace App\Models;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Trade;
use App\Models\Friend;
use App\Models\Message;
use App\Models\UserBan;
use App\Models\Purchase;
use App\Models\Inventory;
use App\Models\StaffUser;
use App\Models\UserBadge;
use App\Models\UserLogin;
use App\Models\ForumReply;
use App\Models\UserAvatar;
use App\Models\ForumThread;
use App\Models\GroupMember;
use App\Models\ItemReseller;
use App\Models\UserSettings;
use App\Models\UsernameHistory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'next_currency_payout',
        'referral_code',
        'referrer_id'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'next_currency_payout' => 'datetime',
        'flood' => 'datetime'
    ];

    public function avatar()
    {
        return UserAvatar::where('user_id', '=', $this->id)->first();
    }

    public function setting()
    {
        return $this->belongsTo('App\Models\UserSettings', 'id');
    }

    public function online()
    {
        return (strtotime($this->updated_at) + 300) > time();
    }

    public function updateFlood()
    {
        $this->flood = Carbon::createFromTimestamp(time() + 15)->toDateTimeString();
        $this->save();
    }

    public function usernameHistory()
    {
        return UsernameHistory::where('user_id', '=', $this->id)->orderBy('created_at', 'ASC')->get();
    }

    public function usernameHistoryString()
    {
        $i = 0;
        $string = '';

        $usernameHistory = $this->usernameHistory();
        $length = $usernameHistory->count();

        foreach ($usernameHistory as $usernameHistoryItem) {
            $i++;
            $string .= $usernameHistoryItem->username;

            if ($i < $length)
                $string .= ', ';
        }

        return $string;
    }

    public function hasMembership()
    {
        return !empty($this->membership_until);
    }

    public function hasVerifiedEmail()
    {
        return !empty($this->email_verified_at);
    }

    public function isWearingItem($id)
    {
        $avatar = $this->avatar();
        $avatar->timestamps = false;

        if ($avatar->hat_1 == $id)
            $column = 'hat_1';
        else if ($avatar->hat_2 == $id)
            $column = 'hat_2';
        else if ($avatar->hat_3 == $id)
            $column = 'hat_3';
        else if ($avatar->head == $id)
            $column = 'head';
        else if ($avatar->face == $id)
            $column = 'face';
        else if ($avatar->gadget == $id)
            $column = 'gadget';
        else if ($avatar->tshirt == $id)
            $column = 'tshirt';
        else if ($avatar->shirt == $id)
            $column = 'shirt';
        else if ($avatar->pants == $id)
            $column = 'pants';
        else
            return false;

        return $avatar->$column == $id;
    }

    public function takeOffItem($id)
    {
        $avatar = $this->avatar();
        $avatar->timestamps = false;

        if ($avatar->hat_1 == $id)
            $column = 'hat_1';
        else if ($avatar->hat_2 == $id)
            $column = 'hat_2';
        else if ($avatar->hat_3 == $id)
            $column = 'hat_3';
        else if ($avatar->head == $id)
            $column = 'head';
        else if ($avatar->face == $id)
            $column = 'face';
        else if ($avatar->gadget == $id)
            $column = 'gadget';
        else if ($avatar->tshirt == $id)
            $column = 'tshirt';
        else if ($avatar->shirt == $id)
            $column = 'shirt';
        else if ($avatar->pants == $id)
            $column = 'pants';
        else
            return false;

        $avatar->$column = null;
        $avatar->save();
    }

    public function moneySpent()
    {
        $purchases = Purchase::where('user_id', '=', $this->id)->get();
        $total = 0.00;

        foreach ($purchases as $purchase)
            $total += (int) $purchase->cost;

        return $total;
    }

    /**
     * Staff
     */

    public function isStaff()
    {
        $permissions = StaffUser::where('user_id', '=', $this->id);

        if (!$permissions->exists())
            return false;

        foreach ($permissions->get() as $name => $permission) {
            if ($name != 'id' && $name != 'user_id' && $name != 'created_at' && $name != 'updated_at') {
                if (!$permission)
                    return false;
            }
        }

        return true;
    }

    public function staff($permission)
    {
        $permissions = StaffUser::where('user_id', '=', $this->id)->first();

        return $permissions->$permission ?? false;
    }

    /**
     * Bans
     */

    public function isBanned()
    {
        return UserBan::where([
            ['user_id', '=', $this->id],
            ['active', '=', true]
        ])->exists();
    }

    public function ban()
    {
        return UserBan::where([
            ['user_id', '=', $this->id],
            ['active', '=', true]
        ])->orderBy('created_at', 'ASC')->first();
    }

    public function bans()
    {
        return UserBan::where('user_id', '=', $this->id)->orderBy('updated_at', 'DESC')->paginate(25);
    }

    /**
     * IPs
     */

    public function ips()
    {
        $log = UserLogin::where('user_id', '=', $this->id)->get();
        $ips = [];

        foreach ($log as $l) {
            if (!in_array($l->ip, $ips))
                $ips[] = $l->ip;
        }

        return $ips;
    }

    public function registerIP()
    {
        $log = UserLogin::where('user_id', '=', $this->id)->orderBy('created_at', 'ASC')->first();

        return $log->ip;
    }

    public function lastIP()
    {
        $log = UserLogin::where('user_id', '=', $this->id)->orderBy('created_at', 'DESC')->first();

        return $log->ip;
    }

    /**
     * Images
     */

    public function thumbnail()
    {
        $url = config('site.storage_url');
        $image = ($this->avatar()->image == 'default') ? config('site.renderer.default_filename') : $this->avatar()->image;

        return "{$url}/{$image}.png";
    }

    public function headshot()
    {
        $url = config('site.storage_url');
        $thumbnail = config('site.official_thumbnail');

        if ($this->id == 1 && $thumbnail)
            return $thumbnail;

        $image = ($this->avatar()->image == 'default') ? config('site.renderer.default_filename') : $this->avatar()->image;

        return "{$url}/{$image}_headshot.png";
    }

    /**
     * Forum
     */

    public function forumLevelMaxExp()
    {
        return 1000 * pow(1.50, $this->forum_level - 1);
    }

    public function forumLevelUp($exp)
    {
        $levelMaxExp = $this->forumLevelMaxExp();

        if ($exp >= $levelMaxExp) {
            $remainingExp = $exp - $levelMaxExp;

            $this->forum_exp = $remainingExp;
            $this->forum_level += 1;
        } else {
            $this->forum_exp = $exp;
        }

        $this->save();
    }

    /**
     * Items
     */

    public function ownsItem($id)
    {
        return Inventory::where([
            ['user_id', '=', $this->id],
            ['item_id', '=', $id]
        ])->exists();
    }

    public function resellableCopiesOfItem($id)
    {
        $i = 1;
        $resellableCopies = [];
        $copies = Inventory::where([
            ['user_id', '=', $this->id],
            ['item_id', '=', $id]
        ])->get();

        foreach ($copies as $copy) {
            $isReselling = ItemReseller::where('inventory_id', '=', $copy->id)->exists();

            if (!$isReselling) {
                $copy->number = $i;
                $resellableCopies[] = $copy;
                $i++;
            }
        }

        return $resellableCopies;
    }

    public function canEditItem($id)
    {
        $item = Item::where('id', '=', $id)->first();

        return ($item->creator_type == 'user' && $this->id == $item->creator->id) || ($item->creator_type == 'group' && $this->id == $item->creator->owner_id);
    }

    /**
     * Groups
     */

    public function groups()
    {
        $members = GroupMember::where('user_id', '=', $this->id)->get();
        $groups = [];

        foreach ($members as $member)
            $groups[] = $member->group->id;

        return Group::whereIn('id', $groups)->get();
    }

    public function hasPrimaryGroup()
    {
        return !empty($this->primary_group_id);
    }

    public function primaryGroup()
    {
        return $this->belongsTo('App\Models\Group', 'primary_group_id');
    }

    public function isInGroup($groupId)
    {
        return GroupMember::where([
            ['user_id', '=', $this->id],
            ['group_id', '=', $groupId]
        ])->exists();
    }

    public function rankInGroup($groupId)
    {
        return GroupMember::where([
            ['user_id', '=', $this->id],
            ['group_id', '=', $groupId]
        ])->first()->rank();
    }

    public function reachedGroupLimit()
    {
        $count = GroupMember::where('user_id', '=', $this->id)->count();
        $limit = (!$this->hasMembership()) ? config('site.group_limit') : config('site.group_limit_membership');

        return $count >= $limit;
    }

    /**
     * Badges
     */

    public function badges()
    {
        $badges = UserBadge::where('user_id', '=', $this->id)->get();
        $array = [];

        foreach ($badges as $badge) {
            $data = config('badges')[$badge->badge_id];

            $badge = new \stdClass;
            $badge->name = $data['name'];
            $badge->description = $data['description'];
            $badge->image = asset("img/badges/{$data['image']}.png");

            $array[] = $badge;
        }

        return $array;
    }

    public function ownsBadge($id)
    {
        return UserBadge::where([
            ['user_id', '=', $this->id],
            ['badge_id', '=', $id]
        ])->exists();
    }

    public function giveBadge($id, $granter = null)
    {
        $badge = new UserBadge;
        $badge->user_id = $this->id;
        $badge->granter_id = $granter;
        $badge->badge_id = $id;
        $badge->save();
    }

    public function removeBadge($id)
    {
        return UserBadge::where([
            ['user_id', '=', $this->id],
            ['badge_id', '=', $id]
        ])->delete();
    }

    /**
     * Friends
     */

    public function friendRequestCount()
    {
        return Friend::where([
            ['receiver_id', '=', $this->id],
            ['is_pending', '=', true]
        ])->count();
    }

    public function friendsOfMine()
    {
        return $this->belongsToMany($this, 'friends', 'sender_id', 'receiver_id');
    }

    public function friendOf()
    {
        return $this->belongsToMany($this, 'friends', 'receiver_id', 'sender_id');
    }

    public function friends()
    {
        return $this->friendsOfMine()->wherePivot('is_pending', '=', false)->orderBy('created_at', 'DESC')->get()->merge($this->friendOf()->wherePivot('is_pending', '=', false)->orderBy('created_at', 'DESC')->get());
    }

    /**
     * Counts
     */

    public function forumPostCount()
    {
        return ForumThread::where([
            ['creator_id', '=', $this->id],
            ['is_deleted', '=', false]
        ])->count() + ForumReply::where([
            ['creator_id', '=', $this->id],
            ['is_deleted', '=', false]
        ])->count();
    }

    public function messageCount()
    {
        return Message::where([
            ['receiver_id', '=', $this->id],
            ['seen', '=', false]
        ])->count();
    }

    public function tradeCount()
    {
        return Trade::where([
            ['receiver_id', '=', $this->id],
            ['status', '=', 'pending']
        ])->count();
    }

    /**
     * Moderation
     */

    public function scrub($column)
    {
        $this->timestamps = false;

        switch ($column) {
            case 'username':
                $contentId = mt_rand(100000, 999999);

                $colors = [
                    'Red',
                    'Blue',
                    'Green',
                    'Black',
                    'White',
                    'Yellow',
                    'Orange',
                    'Pink'
                ];

                $animals = [
                    'Puppy',
                    'Giraffe',
                    'Zebra',
                    'Kitten',
                    'Tiger',
                    'Lion',
                    'Unicorn',
                    'Dolphin',
                    'Shark',
                    'Whale',
                    'Snake',
                    'Dinosaur',
                    'Fish',
                    'Eagle',
                    'Rooster',
                    'Wolf',
                    'Hippo',
                    'Horse',
                    'Turtle',
                    'Squirrel'
                ];

                shuffle($colors);
                shuffle($animals);

                $this->$column = $colors[0] . $animals[0] . $contentId;
                $this->save();
                break;
            case 'description':
            case 'forum_signature':
                $this->$column = '[ Content Removed ]';
                $this->save();
                break;
        }
    }
}
