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

use App\Models\StaffUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    public function index($document)
    {
        $view = $document;
        $variables = [];

        switch ($document) {
            case 'terms':
                $title = 'Terms of Service';
                break;
            case 'privacy':
                $title = 'Privacy Policy';
                break;
            case 'team':
                $title = 'Team';
                $variables['users'] = [];
                $staffUsers = StaffUser::where('user_id', '!=', 1)->orderBy('created_at', 'ASC')->get();

                foreach ($staffUsers as $staffUser)
                    $variables['users'][] = $staffUser->user;
                break;
            default:
                abort(404);
        }

        return view('web.info.index')->with([
            'title' => $title,
            'document' => $document,
            'view' => $view,
            'variables' => $variables
        ]);
    }
}
