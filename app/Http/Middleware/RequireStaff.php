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

namespace App\Http\Middleware;

use Closure;
use App\Models\StaffUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route()->getName();

        if (Str::startsWith($route, 'admin.')) {
            if (!session()->has('staff_user_id') || !session()->has('staff_user_site_id') || !session()->has('staff_user_password')) {
                if (!in_array($route, ['admin.login.index', 'admin.login.authenticate']))
                    return redirect()->route('admin.login.index', ['returnLocation' => $request->url()]);
            } else {
                $user = StaffUser::where([
                    ['id', '=', session('staff_user_id')],
                    ['password', '=', session('staff_user_password')]
                ]);

                if (!$user->exists() || ($user->first()->ping + 600) < time()) {
                    session()->forget('staff_user_id');
                    session()->forget('staff_user_site_id');
                    session()->forget('staff_user_password');

                    return redirect()->route('admin.login.index', ['returnLocation' => $request->url()]);
                }

                $user = $user->first();

                if (($user->ping + 150) < time()) {
                    $user->ping = time();
                    $user->save();
                }
            }
        }

        return $next($request);
    }
}
