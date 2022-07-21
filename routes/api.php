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

Route::group(['namespace' => 'Account', 'prefix' => 'account', 'middleware' => 'auth'], function() {
    Route::group(['prefix' => 'trades'], function() {
        Route::get('/inventory', 'TradesController@inventory');
        Route::post('/process', 'TradesController@process');
    });
});

Route::group(['prefix' => 'catalog'], function() {
    Route::get('/search', 'CatalogController@search');
});

Route::group(['prefix' => 'creator-area'], function() {
    Route::post('/render-preview', 'CreatorAreaController@renderPreview');
});

Route::group(['prefix' => 'groups'], function() {
    Route::get('/members', 'GroupsController@members');
    Route::get('/items', 'GroupsController@items');
    Route::get('/wall', 'GroupsController@wall');
    Route::post('/wall-post', 'GroupsController@wallPost');

    Route::group(['prefix' => 'manage', 'middleware' => 'auth'], function() {
        Route::post('/kick-member', 'GroupsController@kickMember');
        Route::post('/rank-member', 'GroupsController@rankMember');
        Route::post('/payout', 'GroupsController@payout');
    });
});

Route::group(['prefix' => 'search'], function() {
    Route::get('/all', 'SearchController@all');
});

Route::group(['prefix' => 'users'], function() {
    Route::get('/info', 'UsersController@info');
    Route::get('/inventory', 'UsersController@inventory');
});
