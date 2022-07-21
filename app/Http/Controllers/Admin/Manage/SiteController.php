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

namespace App\Http\Controllers\Admin\Manage;

use App\Models\SiteSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next) {
            if (!staffUser()->staff('can_manage_site')) abort(404);

            return $next($request);
        });
    }

    public function index()
    {
        $maintenancePasswords = config('site.maintenance_passwords');

        return view('admin.manage.site')->with([
            'maintenancePasswords' => $maintenancePasswords
        ]);
    }

    public function update(Request $request)
    {
        $site = SiteSettings::where('id', '=', 1)->first();
        $columns = array_keys($site->getAttributes());
        $settings = $request->except('_token', 'alert_message');
        $maintenancePasswords = config('site.maintenance_passwords');

        unset($columns[0]);
        unset($columns[3]);

        try {
            foreach ($columns as $column)
                $site->$column = false;

            foreach ($settings as $name => $value)
                $site->$name = true;

            $site->alert_message = $request->alert_message;
            $site->alert_background_color = $request->alert_background_color;
            $site->alert_text_color = $request->alert_text_color;
            $site->save();

            if ($request->has('maintenance_enabled')) {
                session()->put('maintenance_password', $maintenancePasswords[0]);
                session()->save();
            }
        } catch (Exception $e) {
            return back()->withErrors(['Something went wrong.']);
        }

        return redirect()->route('admin.manage.site.index')->with('success_message', 'Site settings have been updated.');
    }
}
