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

use App\Models\User;
use App\Models\StaffUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $returnLocation = $request->returnLocation;

        return view('admin.login')->with([
            'returnLocation' => $returnLocation
        ]);
    }

    public function authenticate(Request $request)
    {
        $user = User::where('username', '=', $request->username);
        $code = config('site.admin_panel_code') ?? null;

        if ($user->exists() && $user->first()->isStaff() && $request->code == $code) {
            $user = $user->first();

            if (password_verify($request->password, $user->staff('password'))) {
                session()->put('staff_user_id', $user->staff('id'));
                session()->put('staff_user_site_id', $user->id);
                session()->put('staff_user_password', $user->staff('password'));
                session()->save();

                $user = StaffUser::where('user_id', '=', $user->id)->first();
                $user->ping = time();
                $user->save();

                if ($request->has('return_location'))
                    return redirect($request->return_location);

                return redirect()->route('admin.index');
            }
        }

        return back()->withErrors(['The credentials you have provided are wrong!']);
    }

    public function logout()
    {
        session()->forget('staff_user_id');
        session()->forget('staff_user_site_id');
        session()->forget('staff_user_password');

        return redirect()->route('admin.login.index');
    }
}
