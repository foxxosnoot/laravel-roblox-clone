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
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\BanController;

class BannedController extends Controller
{
    public function index()
    {
        $ban = Auth::user()->ban();

        if (!Auth::user()->isBanned())
            abort(404);

        $length = array_search($ban->length, BanController::BAN_LENGTHS);
        $category = array_search($ban->category, BanController::BAN_CATEGORIES);
        $canReactivate = strtotime($ban->banned_until) < time();

        if ($length == 'Close Account')
            $length = 'Account Deleted';

        return view('web.account.banned')->with([
            'ban' => $ban,
            'length' => $length,
            'category' => $category,
            'canReactivate' => $canReactivate
        ]);
    }

    public function reactivate(Request $request)
    {
        $ban = Auth::user()->ban();

        if (!Auth::user()->isBanned())
            abort(404);

        $canReactivate = strtotime($ban->banned_until) < time();

        if (!$request->has('accept'))
            return back()->withErrors(['You must agree to the Terms of Service to continue playing.']);
        else if (!$canReactivate || $ban->length == 'closed')
            return back()->withErrors(['You can not reactivate your account yet.']);

        $ban->active = false;
        $ban->save();

        return redirect()->route('home.dashboard')->with('success_message', 'Account has been reactivated!');
    }
}
