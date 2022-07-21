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

Route::get('/', 'HomeController@index')->name('index');
Route::get('/logout', 'LoginController@logout')->name('logout');

Route::group(['as' => 'login.', 'prefix' => 'login'], function() {
    Route::get('/', 'LoginController@index')->name('index');
    Route::post('/', 'LoginController@authenticate')->name('authenticate');
});

Route::group(['as' => 'create_item.', 'prefix' => 'create-item'], function() {
    Route::get('/{type}', 'CreateItemController@index')->name('index');
    Route::post('/create', 'CreateItemController@create')->name('create');
});

Route::group(['as' => 'edit_item.', 'prefix' => 'edit-item'], function() {
    Route::get('/{id}', 'EditItemController@index')->name('index');
    Route::post('/update', 'EditItemController@update')->name('update');
});

Route::group(['as' => 'users.', 'prefix' => 'users'], function() {
    Route::get('/', 'UsersController@index')->name('index');
    Route::get('/view/{id}', 'UsersController@view')->name('view');
    Route::post('/update', 'UsersController@update')->name('update');

    Route::group(['as' => 'manage.', 'prefix' => 'manage'], function() {
        Route::get('/{type}/{id}', 'ManageUserController@index')->name('index');
        Route::post('/', 'ManageUserController@update')->name('update');
    });

    Route::group(['as' => 'ban.', 'prefix' => 'ban'], function() {
        Route::get('/{id}', 'BanController@index')->name('index');
        Route::post('/', 'BanController@create')->name('create');
    });
});

Route::group(['as' => 'items.', 'prefix' => 'items'], function() {
    Route::get('/', 'ItemsController@index')->name('index');
    Route::get('/view/{id}', 'ItemsController@view')->name('view');
    Route::post('/update', 'ItemsController@update')->name('update');
});

Route::group(['as' => 'asset_approval.', 'prefix' => 'asset-approval'], function() {
    Route::get('/{category}', 'AssetApprovalController@index')->name('index');
    Route::get('/', 'AssetApprovalController@index');
    Route::post('/', 'AssetApprovalController@update')->name('update');
});

Route::group(['as' => 'reports.', 'prefix' => 'reports'], function() {
    Route::get('/', 'ReportsController@index')->name('index');
    Route::post('/', 'ReportsController@update')->name('update');
});

Route::group(['as' => 'manage.', 'prefix' => 'manage', 'namespace' => 'Manage'], function() {
    Route::group(['as' => 'forum_topics.', 'prefix' => 'forum-topics'], function() {
        Route::get('/', 'ForumTopicsController@index')->name('index');;
        Route::get('/new', 'ForumTopicsController@new')->name('new');
        Route::post('/create', 'ForumTopicsController@create')->name('create');
        Route::get('/edit/{id}', 'ForumTopicsController@edit')->name('edit');
        Route::post('/edit', 'ForumTopicsController@update')->name('update');
        Route::get('/delete/{id}', 'ForumTopicsController@confirmDelete')->name('confirm_delete');
        Route::post('/delete', 'ForumTopicsController@delete')->name('delete');
    });

    Route::group(['as' => 'staff.', 'prefix' => 'staff'], function() {
        Route::get('/', 'StaffController@index')->name('index');
        Route::get('/new', 'StaffController@new')->name('new');
        Route::post('/create', 'StaffController@create')->name('create');
        Route::get('/edit/{id}', 'StaffController@edit')->name('edit');
        Route::post('/update', 'StaffController@update')->name('update');
    });

    Route::group(['as' => 'site.', 'prefix' => 'site'], function() {
        Route::get('/', 'SiteController@index')->name('index');
        Route::post('/update', 'SiteController@update')->name('update');
    });
});
