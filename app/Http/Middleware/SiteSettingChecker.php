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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SiteSettingChecker
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
        $middleware = $request->route()->middleware();
        $isPOST = $request->isMethod('post');
        $maintenancePasswords = config('site.maintenance_passwords');

        if ((site_setting('maintenance_enabled') && !in_array(session('maintenance_password'), $maintenancePasswords)) || !site_setting('maintenance_enabled') && session()->has('maintenance_password'))
            session()->forget('maintenance_code');

        if (site_setting('maintenance_enabled') && !session()->has('maintenance_password') && !Str::startsWith($route, 'maintenance.') && $route != 'upgrade.notify')
            return redirect()->route('maintenance.index');

        if (!site_setting('maintenance_enabled') && Str::startsWith($route, 'maintenance.'))
            return $this->disabled('Maintenance', $middleware, $isPOST);

        if (!site_setting('catalog_purchases_enabled') && $route == 'catalog.purchase')
            return $this->disabled('Catalog', $middleware, $isPOST);

        if (!site_setting('forum_enabled') && Str::startsWith($route, 'forum.'))
            return $this->disabled('Forum', $middleware, $isPOST);

        if (!site_setting('create_enabled') && Str::startsWith($route, 'creator_area.'))
            return $this->disabled('Create', $middleware, $isPOST);

        if (!site_setting('character_enabled') && Str::startsWith($route, 'account.character.'))
            return $this->disabled('Character', $middleware, $isPOST);

        if (!site_setting('trading_enabled') && Str::startsWith($route, 'account.trades.'))
            return $this->disabled('Trades', $middleware, $isPOST);

        if (!site_setting('groups_enabled') && Str::startsWith($route, 'groups.'))
            return $this->disabled('Spaces', $middleware, $isPOST);

        if (!site_setting('settings_enabled') && Str::startsWith($route, 'account.settings.'))
            return $this->disabled('Settings', $middleware, $isPOST);

        if (!site_setting('real_life_purchases_enabled') && Str::startsWith($route, 'account.upgrade.'))
            return $this->disabled('Upgrade', $middleware, $isPOST);

        if (!site_setting('registration_enabled') && $route == 'auth.register.authenticate')
            return $this->disabled('Register', $middleware, $isPOST);

        return $next($request);
    }

    public function disabled($feature, $middleware, $isPOST)
    {
        if ($feature == 'Maintenance')
            return redirect()->route('home.index');
        else if (!Auth::check() && in_array('auth', $middleware))
            return redirect()->route('auth.login.index');
        else if (Auth::check() && in_array('guest', $middleware))
            return redirect()->route('home.dashboard');
        else if ($isPOST || ((!Auth::check() || (Auth::check() && !Auth::user()->isStaff())) && in_array('staff', $middleware)))
            return abort(404);

        return response()->view('errors.feature_disabled', ['title' => $feature]);
    }
}
