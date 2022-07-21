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

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'reporter_id',
        'content_id',
        'type',
        'category',
        'comment'
    ];

    public function reporter()
    {
        return $this->belongsTo('App\Models\User', 'reporter_id');
    }

    public function reviewer()
    {
        return $this->belongsTo('App\Models\User', 'reviewer_id');
    }

    public function content()
    {
        switch ($this->type) {
            case 'user':
                return $this->belongsTo('App\Models\User', 'content_id');
            case 'item':
                return $this->belongsTo('App\Models\Item', 'content_id');
            case 'forum-thread':
                return $this->belongsTo('App\Models\ForumThread', 'content_id');
            case 'forum-reply':
                return $this->belongsTo('App\Models\ForumReply', 'content_id');
            case 'group':
                return $this->belongsTo('App\Models\Group', 'content_id');
            case 'group-wall-post':
                return $this->belongsTo('App\Models\GroupWall', 'content_id');
            case 'message':
                return $this->belongsTo('App\Models\Message', 'content_id');
        }
    }

    public function type()
    {
        return ucwords(str_replace('-', ' ', $this->type));
    }

    public function url()
    {
        switch ($this->type) {
            case 'user':
                return route('users.profile', $this->content->username);
            case 'item':
                return route('catalog.item', [$this->content->id, $this->content->slug()]);
            case 'forum-thread':
                return route('forum.thread', $this->content->id);
            case 'forum-reply':
                return route('forum.thread', $this->content->thread_id);
            case 'group':
                return route('groups.view', [$this->content->id, $this->content->slug()]);
        }
    }
}
