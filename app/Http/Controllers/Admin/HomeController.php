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

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $options = [];
        $user = staffUser();

        if ($user->staff('can_create_hat_items'))
            $options[] = [route('admin.create_item.index', 'hat'), 'Create Hat', 'fas fa-hat-cowboy', '#0082ff'];

        if ($user->staff('can_create_face_items'))
            $options[] = [route('admin.create_item.index', 'face'), 'Create Face', 'fas fa-smile', '#0082ff'];

        if ($user->staff('can_create_gadget_items'))
            $options[] = [route('admin.create_item.index', 'gadget'), 'Create Gadget', 'fas fa-hammer', '#0082ff'];

        if ($user->staff('can_view_user_info'))
            $options[] = [route('admin.users.index'), 'Users', 'fas fa-user', '#28a745'];

        if ($user->staff('can_view_item_info'))
            $options[] = [route('admin.items.index'), 'Items', 'fas fa-tshirt', '#28a745'];

        if ($user->staff('can_review_pending_assets'))
            $options[] = [route('admin.asset_approval.index', ''), 'Pending Assets', 'fas fa-image', '#ffc107'];

        if ($user->staff('can_review_pending_reports'))
            $options[] = [route('admin.reports.index'), 'Pending Reports', 'fas fa-flag', '#ffc107'];

        if ($user->staff('can_manage_forum_topics'))
            $options[] = [route('admin.manage.forum_topics.index'), 'Forum Topics', 'fas fa-comments', '#6610f2'];

        if ($user->staff('can_manage_staff'))
            $options[] = [route('admin.manage.staff.index'), 'Staff', 'fas fa-users', '#6610f2'];

        if ($user->staff('can_manage_site'))
            $options[] = [route('admin.manage.site.index'), 'Site Settings', 'fas fa-cog', '#6610f2'];

        return view('admin.index')->with([
            'options' => $options
        ]);
    }
}
