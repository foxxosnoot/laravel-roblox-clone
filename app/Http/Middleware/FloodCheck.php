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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FloodCheck
{
    public const FLOOD_ROUTES = [
        'account.inbox.create',
        'account.settings.update',
        'account.trades.process',
        'report.submit',
        'catalog.update',
        'creator_area.create',
        'catalog.purchase',
        'forum.create',
        'groups.update',
        'jobs.listings.apply'
    ];

    public const API_FLOOD_URLS = [
        'groups/wall-post',
        'groups/manage/payout'
    ];

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
        $url = str_replace("{$request->root()}/api/", '', $request->url());

        if (Auth::check() && $request->isMethod('post') && (in_array($url, $this::API_FLOOD_URLS) || in_array($route, $this::FLOOD_ROUTES))) {
            $seconds = strtotime(Auth::user()->flood) - time();
            $word = ($seconds == 1) ? 'second' : 'seconds';

            if (time() < strtotime(Auth::user()->flood)) {
                if ($route == 'account.trades.process') {
                    if ($request->action == 'send')
                        return response()->json(['error' => "You are trying to do things too quickly! Please wait {$seconds} {$word} before trying again."]);
                } else if ($route == 'jobs.listings.apply') {
                    return response(['errors' => ['name' => ["You are trying to do things too quickly! Please wait {$seconds} {$word} before trying again."]]]);
                } else if ($request->is('api/*')) {
                    return response()->json(['error' => "You are trying to do things too quickly! Please wait {$seconds} {$word} before trying again."]);
                } else {
                    return back()->withErrors(["You are trying to do things too quickly! Please wait {$seconds} {$word} before trying again."]);
                }
            }

            $user = Auth::user();
            $user->flood = Carbon::createFromTimestamp(time() + config('site.flood_time'))->toDateTimeString();
            $user->save();
        }

        return $next($request);
    }
}
