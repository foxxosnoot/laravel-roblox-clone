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

namespace App\Http\Controllers\Web\Account;

use Carbon\Carbon;
use App\Models\Inventory;
use App\Mail\VerifyAccount;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailVerifyHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerifyController extends Controller
{
    public function index()
    {
        $email = EmailVerifyHistory::where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC');
        $emailSent = $email->exists() && ((strtotime($email->first()->created_at) + 600) > time());

        return view('web.account.verify')->with([
            'emailSent' => $emailSent
        ]);
    }

    public function send()
    {
        if (Auth::user()->hasVerifiedEmail())
            return redirect()->route('home.dashboard')->with('success_message', 'You have already verified your email.');

        $code = Str::random(50);
        $email = EmailVerifyHistory::where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC');

        if ($email->exists() && (strtotime($email->first()->created_at) + 600) > time())
            return back()->withErrors(['You have already sent an email in the last 5 minutes.']);

        try {
            $emailVerifyHistory = new EmailVerifyHistory;
            $emailVerifyHistory->user_id = Auth::user()->id;
            $emailVerifyHistory->code = $code;
            $emailVerifyHistory->save();

            Mail::to(Auth::user())->send(new VerifyAccount(Auth::user()));
        } catch (\Exception $err) {
            return back()->withErrors(['Unable to send verification email.']);
        }

        return back()->with('success_message', 'An email has been sent.');
    }

    public function confirm($code)
    {
        if (Auth::user()->hasVerifiedEmail())
            return redirect()->route('home.dashboard')->with('success_message', 'You have already verified your email.');

        $email = EmailVerifyHistory::where([
            ['user_id', '=', Auth::user()->id],
            ['code', '=', $code]
        ])->firstOrFail();

        $email->delete();

        $user = Auth::user();
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();

        if (config('site.email_verification_item_id')) {
            $owns = Auth::user()->ownsItem(config('site.email_verification_item_id'));

            if (!$owns) {
                $inventory = new Inventory;
                $inventory->user_id = Auth::user()->id;
                $inventory->item_id = config('site.email_verification_item_id');
                $inventory->save();
            }
        }

        return redirect()->route('home.dashboard')->with('success_message', 'Your account has been verified');
    }
}
