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

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaintenanceController extends Controller
{
    public function index()
    {
        if (session()->has('maintenance_password'))
            return redirect()->route('home.index');

        return view('web.maintenance.index');
    }

    public function authenticate(Request $request)
    {
        $maintenancePasswords = config('site.maintenance_passwords');

        if (session()->has('maintenance_password'))
            return back()->withErrors(['Already authenticated.']);

        if (!$request->has('password') || empty(trim($request->password)))
            return back()->withErrors(['Please provide a password.']);

        if (!in_array($request->password, $maintenancePasswords))
            return back()->withErrors(['Invalid password.']);

        session()->put('maintenance_password', $request->password);
        session()->save();

        return redirect()->route('home.index');
    }

    public function exit()
    {
        session()->forget('maintenance_password');

        return redirect()->route('maintenance.index');
    }
}
