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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('password');
            $table->integer('ping')->default(0);

            // Item
            $table->boolean('can_view_item_info')->default(false);
            $table->boolean('can_edit_item_info')->default(false);
            $table->boolean('can_create_hat_items')->default(false);
            $table->boolean('can_create_face_items')->default(false);
            $table->boolean('can_create_gadget_items')->default(false);
            $table->boolean('can_create_head_items')->default(false);

            // User
            $table->boolean('can_edit_user_info')->default(false);
            $table->boolean('can_reset_user_passwords')->default(false);
            $table->boolean('can_view_user_info')->default(false);
            $table->boolean('can_view_user_emails')->default(false);
            $table->boolean('can_give_items')->default(false);
            $table->boolean('can_give_currency')->default(false);
            $table->boolean('can_take_items')->default(false);
            $table->boolean('can_take_currency')->default(false);
            $table->boolean('can_ban_users')->default(false);
            $table->boolean('can_unban_users')->default(false);
            $table->boolean('can_ip_ban_users')->default(false);
            $table->boolean('can_ip_unban_users')->default(false);

            // Pending
            $table->boolean('can_review_pending_assets')->default(false);
            $table->boolean('can_review_pending_reports')->default(false);

            // Forum
            $table->boolean('can_edit_forum_posts')->default(false);
            $table->boolean('can_delete_forum_posts')->default(false);
            $table->boolean('can_pin_forum_posts')->default(false);
            $table->boolean('can_lock_forum_posts')->default(false);

            // Jobs
            $table->boolean('can_view_job_listing_responses')->default(false);
            $table->boolean('can_create_job_listings')->default(false);

            // Management
            $table->boolean('can_manage_forum_topics')->default(false);
            $table->boolean('can_manage_staff')->default(false);
            $table->boolean('can_manage_site')->default(false);

            // Etc
            $table->boolean('can_render_thumbnails')->default(false);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_users');
    }
}
