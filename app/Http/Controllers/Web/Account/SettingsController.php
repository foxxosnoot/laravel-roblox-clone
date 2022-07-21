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

namespace App\Http\Controllers\Web\Account;

use Illuminate\Http\Request;
use App\Models\UsernameHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        switch ($request->category) {
            case '':
            case 'general':
            case 'privacy':
            case 'password':
            case 'appearance':
                $category = ($request->category != '') ? $request->category : 'general';
                break;
            default:
                abort(404);
        }

        $files = File::files(public_path('css/themes'));
        $themes = [];

        foreach ($files as $file) {
            $file = str_replace(getcwd(), '', $file);
            $file = str_replace('/css/themes/', '', $file);
            $file = str_replace('\css/themes\\', '', $file);
            $file = str_replace('.css', '', $file);

            $themes[] = $file;
        }

        return view('web.account.settings')->with([
            'category' => $category,
            'themes' => $themes
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        switch ($request->category) {
            case 'general':
                $this->validate($request, [
                    'description' => ['max:1024'],
                    'forum_signature' => ['max:100']
                ]);

                $user->description = $request->description;
                $user->forum_signature = $request->forum_signature;
                $user->save();

                if ($request->username != $user->username) {
                    $price = config('site.username_change_price');
                    $usernameHistory = UsernameHistory::where('username', '=', $request->username);

                    $this->validate($request, [
                        'username' => ['min:3', 'max:20', 'regex:/\\A[a-z\\d]+(?:[.-][a-z\\d]+)*\\z/i', 'unique:users'],
                    ], [
                        'username.unique' => 'Username has already been taken.'
                    ]);

                    if ($usernameHistory->exists() && $usernameHistory->first()->user_id != $user->id)
                        return back()->withErrors(['Username has already been taken.']);

                    if ($user->currency < $price)
                        return back()->withErrors(["You need at least {$price} currency to change your username."]);

                    $usernameHistory = new UsernameHistory;
                    $usernameHistory->user_id = $user->id;
                    $usernameHistory->ip = $user->lastIP();
                    $usernameHistory->username = $user->username;
                    $usernameHistory->save();

                    $user->username = $request->username;
                    $user->currency -= $price;
                    $user->save();

                    return back()->with('success_message', 'Successfully changed your username.');
                }

                if (strtolower($request->email) != strtolower($user->email)) {
                    $this->validate($request, [
                        'email' => ['email', 'max:255', 'unique:users']
                    ]);

                    $user->email = $request->email;
                    $user->email_verified_at = null;
                    $user->save();

                    return back()->with('success_message', 'Successfully updated your email.');
                }

                return back()->with('success_message', 'Successfully updated general settings.');
            case 'privacy':
                $settings = $user->setting;
                $settings->accepts_messages = $request->has('accepts_messages');
                $settings->accepts_friends = $request->has('accepts_friends');
                $settings->accepts_trades = $request->has('accepts_trades');
                $settings->public_inventory = $request->has('public_inventory');
                $settings->save();

                return back()->with('success_message', 'Successfully updated privacy settings.');
            case 'password':
                $this->validate($request, [
                    'current_password' => ['required'],
                    'new_password' => ['required', 'confirmed', 'min:6', 'max:255']
                ]);

                if (!password_verify($request->current_password, $user->password))
                    return back()->withErrors(['Incorrect current password.']);

                $user->password = bcrypt($request->new_password);
                $user->save();

                return back()->with('success_message', 'Successfully updated password.');
            case 'appearance':
                $files = File::files(public_path('css/themes'));
                $themes = [];

                foreach ($files as $file) {
                    $file = str_replace(getcwd(), '', $file);
                    $file = str_replace('/css/themes/', '', $file);
                    $file = str_replace('\css/themes\\', '', $file);
                    $file = str_replace('.css', '', $file);

                    $themes[] = $file;
                }

                if (!in_array($request->theme, $themes))
                    return back()->withErrors(['Invalid theme.']);

                $settings = $user->setting;
                $settings->theme = $request->theme;
                $settings->save();

                return back()->with('success_message', 'Successfully updated appearance settings.');
            default:
                abort(404);
        }
    }
}
