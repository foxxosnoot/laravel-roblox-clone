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

use Carbon\Carbon;
use App\Models\User;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $topics = ForumTopic::where('is_staff_only_viewing', false)->orderBy('home_page_priority', 'DESC')->get();

        if (Auth::check() && Auth::user()->isStaff())
            $topics = ForumTopic::orderBy('home_page_priority', 'DESC')->get();

        return view('web.forum.index')->with([
            'topics' => $topics
        ]);
    }

    public function search(Request $request)
    {
        $search = (isset($request->search)) ? trim($request->search) : '';

        if (!empty($search))
            $threads = ForumThread::where([
                ['title', 'LIKE', "%{$search}%"],
                ['is_deleted', '=', false]
            ])->orderBy('updated_at', 'DESC')->paginate(15);

        return view('web.forum.search')->with([
            'search' => $search,
            'threads' => $threads ?? null
        ]);
    }

    public function topic($id, $slug)
    {
        $topic = ForumTopic::where('id', '=', $id)->firstOrFail();

        if ($slug != $topic->slug() || (!Auth::check() || !Auth::user()->isStaff()) && $topic->is_staff_only_viewing) abort(404);

        return view('web.forum.topic')->with([
            'topic' => $topic
        ]);
    }

    public function thread($id)
    {
        $thread = ForumThread::where('id', '=', $id)->firstOrFail();

        if ((!Auth::check() || !Auth::user()->isStaff()) && ($thread->topic->is_staff_only_viewing || $thread->is_deleted)) abort(404);

        return view('web.forum.thread')->with([
            'thread' => $thread
        ]);
    }

    public function new($type, $id)
    {
        switch ($type) {
            case 'thread':
                $topic = ForumTopic::where('id', '=', $id)->firstOrFail();
                $title = "New Thread in \"{$topic->name}\"";

                if (!Auth::user()->isStaff() && $topic->is_staff_only_posting) abort(404);
                break;
            case 'reply':
                $thread = ForumThread::where('id', '=', $id)->firstOrFail();
                $title = "Reply to \"{$thread->title}\"";

                if (!Auth::user()->isStaff() && ($thread->is_locked || $thread->is_deleted || $thread->topic->is_staff_only_posting)) abort(404);
                break;
            case 'quote':
                $reply = ForumReply::where('id', '=', $id)->firstOrFail();
                $title = "Quote a reply in \"{$reply->thread->title}\"";

                if (!Auth::user()->isStaff() && ($reply->thread->is_locked || $reply->thread->is_deleted || $reply->thread->topic->is_staff_only_posting)) abort(404);
                break;
            default:
                abort(404);
        }

        return view('web.forum.new')->with([
            'title' => $title,
            'type' => $type,
            'id' => $id,
            'quote' => $reply ?? null
        ]);
    }

    public function create(Request $request)
    {
        switch ($request->type) {
            case 'thread':
                $topic = ForumTopic::where('id', '=', $request->id)->firstOrFail();

                if (!Auth::user()->isStaff() && $topic->is_staff_only_posting) abort(404);

                $this->validate($request, [
                    'title' => ['required', 'max:50'],
                    'body' => ['required', 'min:3', 'max:7500']
                ]);

                $thread = new ForumThread;
                $thread->topic_id = $topic->id;
                $thread->creator_id = Auth::user()->id;
                $thread->title = $request->title;
                $thread->body = $request->body;
                $thread->save();

                $wordCount = str_word_count($thread->body);
                $exp = Auth::user()->forum_exp + (($wordCount > 10) ? 10 : $wordCount);

                Auth::user()->forumLevelUp($exp);

                return redirect()->route('forum.thread', $thread->id)->with('success_message', 'Thread has been posted!');
            case 'reply':
                $thread = ForumThread::where('id', '=', $request->id)->firstOrFail();

                if (!Auth::user()->isStaff() && ($thread->is_locked || $thread->is_deleted || $thread->topic->is_staff_only_posting)) abort(404);

                $this->validate($request, [
                    'body' => ['required', 'min:3', 'max:7500']
                ]);

                $reply = new ForumReply;
                $reply->thread_id = $thread->id;
                $reply->creator_id = Auth::user()->id;
                $reply->body = $request->body;
                $reply->save();

                $thread->updated_at = Carbon::now()->toDateTimeString();
                $thread->save();

                $wordCount = str_word_count($reply->body);
                $exp = Auth::user()->forum_exp + (($wordCount > 10) ? 10 : $wordCount);

                Auth::user()->forumLevelUp($exp);

                return redirect()->route('forum.thread', $thread->id)->with('success_message', 'Reply has been posted!');
            case 'quote':
                $reply = ForumReply::where('id', '=', $request->id)->firstOrFail();

                if (!Auth::user()->isStaff() && ($reply->thread->is_locked || $reply->thread->is_deleted || $reply->thread->topic->is_staff_only_posting)) abort(404);

                $this->validate($request, [
                    'body' => ['required', 'min:3', 'max:7500']
                ]);

                $quote = new ForumReply;
                $quote->thread_id = $reply->thread->id;
                $quote->quote_id = $reply->id;
                $quote->creator_id = Auth::user()->id;
                $quote->body = $request->body;
                $quote->save();

                $thread = $reply->thread;
                $thread->updated_at = Carbon::now()->toDateTimeString();
                $thread->save();

                $wordCount = str_word_count($quote->body);
                $exp = Auth::user()->forum_exp + (($wordCount > 10) ? 10 : $wordCount);

                Auth::user()->forumLevelUp($exp);

                return redirect()->route('forum.thread', $reply->thread->id)->with('success_message', 'Reply has been posted!');
            default:
                abort(404);
        }
    }

    public function edit($type, $id)
    {
        if (!Auth::user()->staff('can_edit_forum_posts')) abort(404);

        switch ($type) {
            case 'thread';
                $post = ForumThread::where('id', '=', $id)->firstOrFail();
                $title = "Edit \"{$post->title}\"";
                break;
            case 'reply':
                $post = ForumReply::where('id', '=', $id)->firstOrFail();
                $title = "Edit a Reply to \"{$post->thread->title}\"";
                break;
            default:
                abort(404);
        }

        return view('web.forum.edit')->with([
            'title' => $title,
            'type' => $type,
            'id' => $id,
            'post' => $post
        ]);
    }

    public function update(Request $request)
    {
        if (!Auth::user()->staff('can_edit_forum_posts')) abort(404);

        switch ($request->type) {
            case 'thread':
                $post = ForumThread::where('id', '=', $request->id)->firstOrFail();

                $this->validate($request, [
                    'title' => ['required', 'max:50'],
                    'body' => ['required', 'min:3', 'max:7500']
                ]);

                $post->timestamps = false;
                $post->title = $request->title;
                $post->body = $request->body;
                $post->save();

                return redirect()->route('forum.thread', $post->id)->with('success_message', 'Thread has been edited.');
                break;
            case 'reply':
                $post = ForumReply::where('id', '=', $request->id)->firstOrFail();

                $this->validate($request, [
                    'body' => ['required', 'min:3', 'max:7500']
                ]);

                $post->timestamps = false;
                $post->body = $request->body;
                $post->save();

                return redirect()->route('forum.thread', $post->thread->id)->with('success_message', 'Thread has been edited.');
                break;
            default:
                abort(404);
        }
    }

    public function moderate($type, $action, $id)
    {
        switch ($action) {
            case 'delete':
                if (!Auth::user()->staff('can_delete_forum_posts')) abort(404);

                $post = ($type == 'thread') ? ForumThread::where('id', '=', $id)->firstOrFail() : ForumReply::where('id', '=', $id)->firstOrFail();
                $status = !$post->is_deleted;
                $post->timestamps = false;
                $post->is_deleted = $status;
                $post->save();

                return back()->with('success_message', ($status) ? 'This post has been deleted.' : 'This post has been undeleted.');
            case 'pin':
                if (!Auth::user()->staff('can_pin_forum_posts') || $type != 'thread') abort(404);

                $thread = ForumThread::where('id', '=', $id)->firstOrFail();
                $status = !$thread->is_pinned;
                $thread->timestamps = false;
                $thread->is_pinned = $status;
                $thread->save();

                return back()->with('success_message', ($status) ? 'This thread has been pinned.' : 'This thread has been unpinned.');
            case 'lock':
                if (!Auth::user()->staff('can_lock_forum_posts') || $type != 'thread') abort(404);

                $thread = ForumThread::where('id', '=', $id)->firstOrFail();
                $status = !$thread->is_locked;
                $thread->timestamps = false;
                $thread->is_locked = $status;
                $thread->save();

                return back()->with('success_message', ($status) ? 'This thread has been locked.' : 'This thread has been unlocked.');
            default:
                abort(404);
        }
    }
}
