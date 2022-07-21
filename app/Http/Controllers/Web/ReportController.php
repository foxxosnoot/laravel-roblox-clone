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

use App\Models\Item;
use App\Models\User;
use App\Models\Group;
use App\Models\Report;
use App\Models\Message;
use App\Models\GroupWall;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ReportsController;

class ReportController extends Controller
{
    public function index($type, $id)
    {
        $title = 'Report ' . ucwords(str_replace('-', ' ', $type));
        $categories = ReportsController::REPORT_CATEGORIES_LONG;

        switch ($type) {
            case 'user':
                $content = User::where('username', '=', $id)->firstOrFail();
                $fields = [
                    'Username' => $content->username,
                    'Description' => $content->description ?? 'This user does not have a description.'
                ];

                if ($content->id == Auth::user()->id || $content->isBanned() || $content->isStaff()) abort(404);
                break;
            case 'item':
                $content = Item::where('id', '=', $id)->firstOrFail();
                $title = 'Report Item';
                $fields = [
                    'Creator' => "<a href=\"{$content->creatorUrl()}\">{$content->creatorName()}</a>",
                    'Name' => $content->name,
                    'Description' => $content->description ?? 'This item does not have a description.'
                ];

                if ($content->creator_type == 'user' && ($content->creator->id == Auth::user()->id || $content->creator->isStaff())) abort(404);
                break;
            case 'forum-thread':
                $content = ForumThread::where('id', '=', $id)->firstOrFail();
                $url = route('users.profile', $content->creator->username);
                $fields = [
                    'Poster' => "<a href=\"{$url}\">{$content->creator->username}</a>",
                    'Title' => $content->title,
                    'Body' => $content->body
                ];

                if ($content->creator->id == Auth::user()->id || $content->is_deleted || $content->creator->isStaff()) abort(404);
                break;
            case 'forum-reply':
                $content = ForumReply::where('id', '=', $id)->firstOrFail();
                $url = route('users.profile', $content->creator->username);
                $fields = [
                    'Poster' => "<a href=\"{$url}\">{$content->creator->username}</a>",
                    'Body' => $content->body
                ];

                if ($content->creator->id == Auth::user()->id || $content->is_deleted || $content->creator->isStaff()) abort(404);
                break;
            case 'group':
                $content = Group::where('id', '=', $id)->firstOrFail();
                $url = route('users.profile', $content->owner->username);
                $fields = [
                    'Owner' => "<a href=\"{$url}\">{$content->owner->username}</a>",
                    'Name' => $content->name,
                    'Description' => $content->body ?? 'This group does not have a description.'
                ];

                if ($content->owner->id == Auth::user()->id || $content->is_locked || $content->owner->isStaff()) abort(404);
                break;
            case 'group-wall-post':
                $content = GroupWall::where('id', '=', $id)->firstOrFail();
                $url = route('users.profile', $content->user->username);
                $fields = [
                    'Poster' => "<a href=\"{$url}\">{$content->user->username}</a>",
                    'Body' => $content->body
                ];

                if ($content->user->id == Auth::user()->id || $content->user->isStaff()) abort(404);
                break;
            case 'message':
                $content = Message::where('id', '=', $id)->firstOrFail();
                $url = route('users.profile', $content->sender->username);
                $fields = [
                    'Sender' => "<a href=\"{$url}\">{$content->sender->username}</a>",
                    'Name' => $content->title,
                    'Body' => $content->body
                ];

                if ($content->sender->id == Auth::user()->id || $content->sender->isStaff() || $content->receiver->id != Auth::user()->id) abort(404);
                break;
            default:
                abort(404);
        }

        return view('web.report.index')->with([
            'title' => $title,
            'type' => $type,
            'fields' => $fields,
            'categories' => $categories,
            'content' => $content
        ]);
    }

    public function thankYou()
    {
        return view('web.report.thank_you');
    }

    public function submit(Request $request)
    {
        switch ($request->type) {
            case 'user':
                $content = User::where('id', '=', $request->id)->firstOrFail();

                if ($content->id == Auth::user()->id || $content->isBanned() || $content->isStaff()) abort(404);
                break;
            case 'item':
                $content = Item::where('id', '=', $request->id)->firstOrFail();

                if ($content->creator_type == 'user' && ($content->creator->id == Auth::user()->id || $content->creator->isStaff())) abort(404);
                break;
            case 'forum-thread':
                $content = ForumThread::where('id', '=', $request->id)->firstOrFail();

                if ($content->creator->id == Auth::user()->id || $content->is_deleted || $content->creator->isStaff()) abort(404);
                break;
            case 'forum-reply':
                $content = ForumReply::where('id', '=', $request->id)->firstOrFail();

                if ($content->creator->id == Auth::user()->id || $content->is_deleted || $content->creator->isStaff()) abort(404);
                break;
            case 'group':
                $content = Group::where('id', '=', $request->id)->firstOrFail();

                if ($content->owner->id == Auth::user()->id || $content->is_locked || $content->owner->isStaff()) abort(404);
                break;
            case 'group-wall-post':
                $content = GroupWall::where('id', '=', $request->id)->firstOrFail();

                if ($content->user->id == Auth::user()->id || $content->user->isStaff()) abort(404);
                break;
            case 'message':
                $content = Message::where('id', '=', $request->id)->firstOrFail();

                if ($content->sender->id == Auth::user()->id || $content->sender->isStaff() || $content->receiver->id != Auth::user()->id) abort(404);
                break;
            default:
                abort(404);
        }

        $this->validate($request, [
            'comment' => ['max:250']
        ]);

        if (!in_array($request->category, ReportsController::REPORT_CATEGORIES))
            return back()->withErrors(['Invalid category.']);

        $report = new Report;
        $report->reporter_id = Auth::user()->id;
        $report->content_id = $content->id;
        $report->type = $request->type;
        $report->category = $request->category;
        $report->comment = $request->comment;
        $report->save();

        return redirect()->route('report.thank_you');
    }
}
