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

namespace App\Http\Controllers\Admin;

use App\Models\Item;
use App\Models\User;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageUserController extends Controller
{
    public function index($type, $id)
    {
        $user = User::where('id', '=', $id)->firstOrFail();

        switch ($type) {
            case 'currency':
                $title = "Manage {$user->username}'s Currency";

                if (!staffUser()->staff('can_give_currency') || !staffUser()->staff('can_take_currency')) abort(404);
                break;
            case 'inventory':
                $title = "Manage {$user->username}'s Inventory";

                if (!staffUser()->staff('can_give_items') || !staffUser()->staff('can_take_items')) abort(404);
                break;
            default:
                abort(404);
        }

        return view('admin.users.manage')->with([
            'title' => $title,
            'type' => $type,
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $user = User::where('id', '=', $request->id)->firstOrFail();
        $user->timestamps = false;

        switch ($request->action) {
            case 'give_currency':
                if (!staffUser()->staff('can_give_currency')) abort(404);

                $this->validate($request, [
                    'amount' => ['required', 'numeric', 'min:1', 'max:1000000']
                ]);

                $user->currency += $request->amount;
                $user->save();

                return redirect()->route('admin.users.view', $user->id)->with('success_message', "User has been given {$request->currency} currency.");
            case 'take_currency':
                if (!staffUser()->staff('can_take_currency')) abort(404);

                $this->validate($request, [
                    'amount' => ['required', 'numeric', 'min:1']
                ]);

                if ($request->amount > $user->currency)
                    return back()->withErrors(['User does not have this much currency.']);

                $user->currency -= $request->amount;
                $user->save();

                return redirect()->route('admin.users.view', $user->id)->with('success_message', "{$request->amount} currency has been taken from this user.");
            case 'give_items':
                if (!staffUser()->staff('can_give_items')) abort(404);

                $this->validate($request, [
                    'item_id' => ['required', 'numeric', 'min:1']
                ]);

                $item = Item::where('id', '=', $request->item_id);

                if (!$item->exists())
                    return back()->withErrors(['This item does not exist.']);

                $item = $item->first();

                $inventory = new Inventory;
                $inventory->user_id = $user->id;
                $inventory->item_id = $item->id;
                $inventory->save();

                return redirect()->route('admin.users.view', $user->id)->with('success_message', "User has been given the \"{$item->name}\" item.");
            case 'take_items':
                if (!staffUser()->staff('can_take_items')) abort(404);

                $this->validate($request, [
                    'item_id' => ['required', 'numeric', 'min:1']
                ]);

                $item = Item::where('id', '=', $request->item_id);

                if (!$item->exists())
                    return back()->withErrors(['This item does not exist.']);

                $item = $item->first();

                if (!$user->ownsItem($item->id))
                    return back()->withErrors(['User does not own this item.']);

                $inventory = Inventory::where([
                    ['user_id', '=', $user->id],
                    ['item_id', '=', $item->id]
                ])->first();
                $inventory->delete();

                return redirect()->route('admin.users.view', $user->id)->with('success_message', "The \"{$item->name}\" item has been taken from this user.");
            default:
                abort(404);
        }
    }
}
