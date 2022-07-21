<!--
MIT License

Copyright (c) 2021-2022 FoxxoSnoot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
-->

@extends('layouts.admin', [
    'title' => "Edit \"{$user->user->username}\""
])

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.manage.staff.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $user->user->id }}">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Item</strong>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_view_item_info" @if ($user->can_view_item_info) checked @endif>
                            <label class="form-check-label" for="can_view_item_info">Can View Item Info</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_edit_item_info" @if ($user->can_edit_item_info) checked @endif>
                            <label class="form-check-label" for="can_edit_item_info">Can Edit Item Info</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_create_hat_items" @if ($user->can_create_hat_items) checked @endif>
                            <label class="form-check-label" for="can_create_hat_items">Can Create Hat Items</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_create_face_items" @if ($user->can_create_face_items) checked @endif>
                            <label class="form-check-label" for="can_create_face_items">Can Create Face Items</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_create_gadget_items" @if ($user->can_create_gadget_items) checked @endif>
                            <label class="form-check-label" for="can_create_gadget_items">Can Create Gadget Items</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>User</strong>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_edit_user_info" @if ($user->can_edit_user_info) checked @endif>
                            <label class="form-check-label" for="can_edit_user_info">Can Edit User Info</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_reset_user_passwords" @if ($user->can_reset_user_passwords) checked @endif>
                            <label class="form-check-label" for="can_reset_user_passwords">Can Reset User Passwords</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_view_user_info" @if ($user->can_view_user_info) checked @endif>
                            <label class="form-check-label" for="can_view_user_info">Can View User Info</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_view_user_emails" @if ($user->can_view_user_emails) checked @endif>
                            <label class="form-check-label" for="can_view_user_emails">Can View User Emails</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_give_items" @if ($user->can_give_items) checked @endif>
                            <label class="form-check-label" for="can_give_items">Can Give Items</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_give_currency" @if ($user->can_give_currency) checked @endif>
                            <label class="form-check-label" for="can_give_currency">Can Give Currency</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_take_items" @if ($user->can_take_items) checked @endif>
                            <label class="form-check-label" for="can_take_items">Can Take Items</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_take_currency" @if ($user->can_take_currency) checked @endif>
                            <label class="form-check-label" for="can_take_currency">Can Take Currency</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_ban_users" @if ($user->can_ban_users) checked @endif>
                            <label class="form-check-label" for="can_ban_users">Can Ban Users</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_unban_users" @if ($user->can_unban_users) checked @endif>
                            <label class="form-check-label" for="can_unban_users">Can Unban Users</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_ip_ban_users" @if ($user->can_ip_ban_users) checked @endif>
                            <label class="form-check-label" for="can_ip_ban_users">Can Ban IPs</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_ip_unban_users" @if ($user->can_ip_unban_users) checked @endif>
                            <label class="form-check-label" for="can_ip_unban_users">Can Unban IPs</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>Pending</strong>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_review_pending_assets" @if ($user->can_review_pending_assets) checked @endif>
                            <label class="form-check-label" for="can_review_pending_assets">Can Review Pending Assets</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_review_pending_reports" @if ($user->can_review_pending_reports) checked @endif>
                            <label class="form-check-label" for="can_review_pending_reports">Can Review Pending Reports</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>Forum</strong>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_edit_forum_posts" @if ($user->can_edit_forum_posts) checked @endif>
                            <label class="form-check-label" for="can_edit_forum_posts">Can Edit Forum Posts</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_delete_forum_posts" @if ($user->can_delete_forum_posts) checked @endif>
                            <label class="form-check-label" for="can_delete_forum_posts">Can Delete Forum Posts</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_pin_forum_posts" @if ($user->can_pin_forum_posts) checked @endif>
                            <label class="form-check-label" for="can_pin_forum_posts">Can Pin Forum Posts</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_lock_forum_posts" @if ($user->can_lock_forum_posts) checked @endif>
                            <label class="form-check-label" for="can_lock_forum_posts">Can Lock Forum Posts</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>Management</strong>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_manage_forum_topics" @if ($user->can_manage_forum_topics) checked @endif>
                            <label class="form-check-label" for="can_manage_forum_topics">Can Manage Forum Topics</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_manage_staff" @if ($user->can_manage_staff) checked @endif>
                            <label class="form-check-label" for="can_manage_staff">Can Manage Staff</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_manage_site" @if ($user->can_manage_site) checked @endif>
                            <label class="form-check-label" for="can_manage_site">Can Manage Site</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>Etc</strong>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="can_render_thumbnails" @if ($user->can_render_thumbnails) checked @endif>
                            <label class="form-check-label" for="can_render_thumbnails">Can Render Thumbnails</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col">
                        <button class="btn btn-block btn-success" type="submit">Update</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-block btn-warning" name="password">Reset Password</button>
                    </div>
                    @if ($user->user->id != staffUser()->id)
                        <div class="col">
                            <button class="btn btn-block btn-danger" name="delete">Delete</button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
