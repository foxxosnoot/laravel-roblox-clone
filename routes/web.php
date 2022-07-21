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

Route::group(['as' => 'home.'], function() {
    Route::get('/', 'HomeController@index')->name('index')->middleware('guest');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard')->middleware('auth');
    Route::get('/admin', 'HomeController@admin')->name('admin')->middleware('auth');
});

Route::group(['as' => 'info.', 'prefix' => 'info'], function() {
    Route::get('/{article}', 'InfoController@index')->name('index');
});

Route::group(['as' => 'maintenance.', 'prefix' => 'maintenance'], function() {
    Route::get('/', 'MaintenanceController@index')->name('index');
    Route::post('/', 'MaintenanceController@authenticate')->name('authenticate');
    Route::get('/exit', 'MaintenanceController@exit')->name('exit');
});

Route::group(['as' => 'auth.', 'namespace' => 'Auth'], function() {
    Route::get('/logout', 'LoginController@logout')->name('logout');

    Route::group(['middleware' => 'guest'], function() {
        Route::group(['as' => 'login.', 'prefix' => 'login'], function() {
            Route::get('/', 'LoginController@index')->name('index');
            Route::post('/', 'LoginController@authenticate')->name('authenticate');
        });

        Route::group(['as' => 'register.', 'prefix' => 'register'], function() {
            Route::get('/', 'RegisterController@index')->name('index');
            Route::get('/{referralCode}', 'RegisterController@index')->name('referred');
            Route::post('/', 'RegisterController@authenticate')->name('authenticate');
        });
    });
});

Route::group(['as' => 'account.', 'namespace' => 'Account', 'prefix' => 'account'], function() {
    Route::group(['as' => 'upgrade.', 'prefix' => 'upgrade'], function() {
        Route::post('/notify', 'UpgradeController@notify')->name('notify');

        Route::group(['middleware' => 'auth'], function() {
            Route::get('/', 'UpgradeController@index')->name('index');
            Route::get('/checkout/{product}', 'UpgradeController@checkout')->name('checkout');
            Route::get('/thank-you', 'UpgradeController@thankYou')->name('thank_you');
            Route::get('/canceled', 'UpgradeController@canceled')->name('canceled');
        });
    });

    Route::group(['middleware' => 'auth'], function() {
        Route::group(['as' => 'verify.', 'prefix' => 'verify'], function() {
            Route::get('/', 'VerifyController@index')->name('index');
            Route::get('/confirm/{code}', 'VerifyController@confirm')->name('confirm');
            Route::post('/send', 'VerifyController@send')->name('send');
        });

        Route::group(['as' => 'banned.', 'prefix' => 'account-suspended'], function() {
            Route::get('/', 'BannedController@index')->name('index');
            Route::post('/', 'BannedController@reactivate')->name('reactivate');
        });

        Route::group(['as' => 'character.', 'prefix' => 'character'], function() {
            Route::get('/', 'CharacterController@index')->name('index');
            Route::post('/regenerate', 'CharacterController@regenerate')->name('regenerate');
            Route::get('/inventory', 'CharacterController@inventory')->name('inventory');
            Route::get('/wearing', 'CharacterController@wearing')->name('wearing');
            Route::post('/update', 'CharacterController@update')->name('update');
        });

        Route::group(['as' => 'discord.', 'prefix' => 'discord'], function() {
            Route::get('/', 'DiscordController@index')->name('index');
            Route::post('/', 'DiscordController@generate')->name('generate');
        });

        Route::group(['as' => 'settings.', 'prefix' => 'settings'], function() {
            Route::get('/', 'SettingsController@index');
            Route::get('/{category}', 'SettingsController@index')->name('index');
            Route::post('/', 'SettingsController@update')->name('update');
        });

        Route::group(['as' => 'friends.', 'prefix' => 'friends'], function() {
            Route::get('/', 'FriendsController@index')->name('index');
            Route::post('/', 'FriendsController@update')->name('update');
        });

        Route::group(['as' => 'inbox.', 'prefix' => 'inbox'], function() {
            Route::get('/', 'InboxController@index');
            Route::get('/{category}', 'InboxController@index')->name('index');
            Route::get('/view/{id}', 'InboxController@message')->name('message');
            Route::get('/new/{type}/{id}', 'InboxController@new')->name('new');
            Route::post('/create', 'InboxController@create')->name('create');
        });

        Route::group(['as' => 'money.', 'prefix' => 'money'], function() {
            Route::get('/', 'MoneyController@index');
            Route::get('/{category}', 'MoneyController@index')->name('index');
        });

        Route::group(['as' => 'trades.', 'prefix' => 'trades'], function() {
            Route::get('/', 'TradesController@index');
            Route::get('/{category}', 'TradesController@index')->name('index');
            Route::get('/view/{id}', 'TradesController@view')->name('view');
            Route::get('/send/{username}', 'TradesController@send')->name('send');
            Route::post('/process', 'TradesController@process')->name('process');
        });

        Route::group(['as' => 'invite.', 'prefix' => 'invite'], function() {
            Route::get('/', 'InviteController@index')->name('index');
        });

        Route::group(['as' => 'promocodes.', 'prefix' => 'promocodes'], function() {
            Route::get('/', 'PromocodesController@index')->name('index');
            Route::post('/redeem', 'PromocodesController@redeem')->name('redeem');
        });
    });
});

Route::group(['as' => 'report.', 'prefix' => 'report', 'middleware' => 'auth'], function() {
    Route::get('/{type}/{id}', 'ReportController@index')->name('index');
    Route::get('/thank-you', 'ReportController@thankYou')->name('thank_you');
    Route::post('/submit', 'ReportController@submit')->name('submit');
});

Route::group(['as' => 'catalog.', 'prefix' => 'catalog'], function() {
    Route::get('/', 'CatalogController@index')->name('index');
    Route::get('/{id}/{slug}', 'CatalogController@item')->name('item');
    Route::get('/{id}/{slug}/edit', 'CatalogController@edit')->name('edit')->middleware('auth');
    Route::post('/update', 'CatalogController@update')->name('update')->middleware('auth');
    Route::post('/purchase', 'CatalogController@purchase')->name('purchase')->middleware('auth');
    Route::post('/resell', 'CatalogController@resell')->name('resell')->middleware('auth');
    Route::post('/take-off-sale', 'CatalogController@takeOffSale')->name('take_off_sale')->middleware('auth');
});

Route::group(['as' => 'forum.', 'prefix' => 'forum'], function() {
    Route::get('/', 'ForumController@index')->name('index');
    Route::get('/search', 'ForumController@search')->name('search')->middleware('auth');
    Route::get('/topic/{id}/{slug}', 'ForumController@topic')->name('topic');
    Route::get('/thread/{id}', 'ForumController@thread')->name('thread');
    Route::get('/new/{type}/{id}', 'ForumController@new')->name('new')->middleware('auth');
    Route::post('/create', 'ForumController@create')->name('create')->middleware('auth');
    Route::get('/edit/{type}/{id}', 'ForumController@edit')->name('edit')->middleware('require_staff');
    Route::post('/edit', 'ForumController@update')->name('update')->middleware('require_staff');
    Route::get('/moderate/{type}/{action}/{id}', 'ForumController@moderate')->name('moderate')->middleware('require_staff');
});

Route::group(['as' => 'creator_area.', 'prefix' => 'create', 'middleware' => 'auth'], function() {
    Route::get('/', 'CreatorAreaController@index')->name('index');
    Route::post('/create', 'CreatorAreaController@create')->name('create');
});

Route::group(['as' => 'users.'], function() {
    Route::group(['prefix' => 'users'], function() {
        Route::get('/search/{category}', 'UsersController@index')->name('index');
        Route::get('/search', 'UsersController@index');
    });

    Route::group(['prefix' => 'profile/{username}'], function() {
        Route::get('/', 'UsersController@profile')->name('profile');
        Route::get('/friends', 'UsersController@friends')->name('friends');
        Route::get('/groups', 'UsersController@groups')->name('groups');
    });
});

Route::group(['as' => 'groups.', 'prefix' => 'groups'], function() {
    Route::get('/', 'GroupsController@index')->name('index');
    Route::get('/{id}/{slug}', 'GroupsController@view')->name('view');
    Route::get('/{id}/{slug}/manage', 'GroupsController@manage')->name('manage')->middleware('auth');
    Route::post('/update', 'GroupsController@update')->name('update')->middleware('auth');
    Route::post('/update-join-request', 'GroupsController@updateJoinRequest')->name('update_join_request')->middleware('auth');
    Route::post('/membership', 'GroupsController@membership')->name('membership')->middleware('auth');
    Route::post('/set-primary', 'GroupsController@setPrimary')->name('set_primary')->middleware('auth');
});

Route::group(['as' => 'badges.', 'prefix' => 'badges'], function() {
    Route::get('/', 'BadgesController@index')->name('index');
});
