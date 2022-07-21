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
use App\Models\User;
use App\Models\IPBan;
use App\Models\Inventory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next) {
            if (!staffUser()->staff('can_view_user_info')) abort(404);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $search = (isset($request->search)) ? trim($request->search) : '';
        $users = User::where('username', 'LIKE', "%{$search}%")->orderBy('created_at', 'ASC')->paginate(25);

        return view('admin.users.index')->with([
            'search' => $search,
            'users' => $users ?? null
        ]);
    }

    public function view($id)
    {
        $user = User::where('id', '=', $id)->firstOrFail();
        $ipBans = IPBan::where('unbanner_id', '=', null)->whereIn('ip', $user->ips())->get();
        $ipBanned = $ipBans->count() > 0;

        return view('admin.users.view')->with([
            'user' => $user,
            'ipBanned' => $ipBanned
        ]);
    }

    public function update(Request $request)
    {
        $user = User::where('id', '=', $request->id)->firstOrFail();
        $user->timestamps = false;

        switch ($request->action) {
            case 'unban':
                if (!staffUser()->staff('can_unban_users')) abort(404);

                $ban = $user->ban();
                $ban->unbanner_id = staffUser()->id;
                $ban->active = false;
                $ban->save();

                return back()->with('success_message', 'User has been unbanned.');
            case 'password':
                if (!staffUser()->staff('can_reset_user_passwords')) abort(404);

                $password = Str::random(25);

                $user->password = bcrypt($password);
                $user->save();

                return back()->with('success_message', "User password has been changed to <strong>{$password}</strong>.");
            case 'ip_ban':
                $ipBans = IPBan::where('unbanned_id', '=', null)->whereIn('ip', $user->ips())->get();
                $ipBanned = $ipBans->count() > 0;
                $message = 'User has been IP banned.';

                if ($ipBanned) {
                    $message = 'User is no longer IP banned.';

                    foreach ($user->ips() as $ip)
                        $exists = IPBan::where('ip', '=', $ip)->exists();

                        if ($exists) {
                            $ipBan = IPBan::where('ip', '=', $ip)->first();
                            $ipBan->unbanner_id = staffUser()->id;
                            $ipBan->save();
                        }
                } else {
                    foreach ($user->ips() as $ip) {
                        $ipBan = new IPBan;
                        $ipBan->banner_id = staffUser()->id;
                        $ipBan->ip = $ip;
                        $ipBan->save();
                    }
                }

                return back()->with('success_message', $message);
            case 'scrub_username':
            case 'scrub_description':
            case 'scrub_forum_signature':
                if (!staffUser()->staff('can_edit_user_info')) abort(404);

                $column = str_replace('scrub_', '', $request->action);
                $word = ucfirst(str_replace('_', ' ', $column));

                $user->scrub($column);

                return back()->with('success_message', "{$word} has been scrubbed.");
            case 'remove_membership':
                if (!staffUser()->staff('can_edit_user_info')) abort(404);

                if (!$user->hasMembership())
                    return back()->withErrors(['This user does not have a membership.']);

                $user->membership_until = null;
                $user->save();
                $user->removeBadge(9);

                return back()->with('success_message', 'User membership has been removed.');
            case 'grant_membership':
                if (!staffUser()->staff('can_edit_user_info')) abort(404);

                switch ($request->membership_length) {
                    case '1_month':
                        $months = 1;
                        break;
                    case '3_months':
                        $months = 3;
                        break;
                    case '6_months':
                        $months = 6;
                        break;
                    case '1_year':
                        $months = 12;
                        break;
                    case 'forever':
                        $months = 150;
                        break;
                    default:
                        abort(404);
                }

                $time = ($request->membership_length == 'forever') ? 'a lifetime' : str_replace('_', ' ', $request->membership_length);

                $user->membership_until = Carbon::now()->addMonths($months)->toDateTimeString();
                $user->save();
                $user->giveBadge(9);

                if (config('site.membership_item_id')) {
                    $owns = $user->ownsItem(config('site.membership_item_id'));

                    if (!$owns) {
                        $inventory = new Inventory;
                        $inventory->user_id = $user->id;
                        $inventory->item_id = config('site.membership_item_id');
                        $inventory->save();
                    }
                }

                return back()->with('success_message', "User has been granted {$time} worth of membership.");
            case 'regen':
                if (!staffUser()->staff('can_render_thumbnails')) abort(404);

                render($user->id, 'user');

                return back()->with('success_message', 'User thumbnail has been regenerated.');
            case 'reset':
                if (!staffUser()->staff('can_render_thumbnails')) abort(404);

                $user->avatar()->reset();

                return back()->with('success_message', 'User avatar has been reset.');
            default:
                abort(404);
        }
    }
}
