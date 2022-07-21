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

return [
    'name' => env('APP_NAME', 'Laravel Roblox Clone'),
    'logo' => '/img/logo.png',
    'icon' => '/img/icon.png',

    'paypal_email' => 'email@gmail.com',
    'paypal_sandbox' => false,

    'route_domains' => [
        'admin_site' => 'panel.domain.com',
        'main_site' => 'www.domain.com'
    ],

    'storage_url' => 'http://cdn.domain.com',
    'referral_url' => 'https://refer.domain.com',

    'official_thumbnail' => '/img/icon.png', // Headshot for the system account (ID 1)

    'updates_forum_topic_id' => 1,

    'username_change_price' => 250,
    'group_creation_price' => 50,

    'flood_time' => 15,

    'daily_currency' => 10,
    'daily_currency_membership' => 25,
    'group_limit' => 10,
    'group_limit_membership' => 25,

    'donator_item_id' => 0,
    'membership_item_id' => 0,
    'email_verification_item_id' => 0,
    'fake_admin_item_id' => 0, // Granted to those who visit "/admin", leave as 0 if none

    'membership_name' => 'Gold',
    'membership_color' => '#000',
    'membership_bg_color' => '#ffc113',

    'renderer' => [
        'url' => 'https://renderer.domain.com',
        'key' => 'secretkey',
        'default_filename' => 'user', // Default thumbnail filename
        'previews_enabled' => true
    ],

    'socials' => [
        'discord' => '',
        'twitter' => ''
    ],

    'admin_panel_code' => '', // A second layer of protection to the administration panel required to login, leave empty if you do not want one
    'maintenance_passwords' => [
        'key'
    ],

    'catalog_recent_item_types' => ['hat', 'face', 'gadget'],
    'catalog_item_types' => ['hat', 'face', 'gadget', 'shirt', 'pants'],
    'inventory_item_types' => ['hat', 'face', 'gadget', 'shirt', 'pants'],
    'character_editor_item_types' => ['hat', 'face', 'gadget', 'shirt', 'pants'],
    'item_thumbnails_with_padding' => ['hat', 'face', 'gadget', 'shirt', 'pants']
];
