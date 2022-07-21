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

use App\Models\ForumThread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class ForumTopic extends Model
{
    use HasFactory;

    protected $table = 'forum_topics';

    protected $fillable = [
        'name',
        'description',
        'home_page_priority',
        'is_staff_only_viewing',
        'is_staff_only_posting'
    ];

    public function threads($hasPagination = true)
    {
        if (Auth::check() && Auth::user()->isStaff())
            $threads = ForumThread::where('topic_id', '=', $this->id)->orderBy('is_pinned', 'DESC')->orderBy('updated_at', 'DESC');
        else
            $threads = ForumThread::where([
                ['topic_id', '=', $this->id],
                ['is_deleted', '=', false]
            ])->orderBy('is_pinned', 'DESC')->orderBy('updated_at', 'DESC');

        return ($hasPagination) ? $threads->paginate(15) : $threads->get();
    }

    public function lastPost()
    {
        return ForumThread::where([
            ['topic_id', '=', $this->id],
            ['is_deleted', '=', false]
        ])->orderBy('updated_at', 'DESC')->first();
    }

    public function slug()
    {
        $name = str_replace('-', ' ', $this->name);

        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
}
