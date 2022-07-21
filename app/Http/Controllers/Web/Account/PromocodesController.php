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

use App\Models\Item;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PromocodesController extends Controller
{
    public function index()
    {
        $codes = config('promocodes');
        $items = [];

        foreach ($codes as $code => $id) {
            $item = Item::where('id', '=', $id);
            
            if ($item->exists())
                $items[] = $item->first();
        }

        return view('web.account.promocodes')->with([
            'items' => $items
        ]);
    }

    public function redeem(Request $request)
    {
        $code = strtoupper($request->code);
        $codes = config('promocodes');

        if (!$request->has('code'))
            return response()->json(['error' => 'Please provide a code to redeem.']);

        if (!array_key_exists($code, $codes))
            return response()->json(['error' => "Invalid code. This code doesn't exist or has expired."]);

        $item = $codes[$code];
        $owns = Auth::user()->ownsItem($item);

        if ($owns)
            return response()->json(['error' => 'This code has already been redeemed on your account.']);

        $inventory = new Inventory;
        $inventory->user_id = Auth::user()->id;
        $inventory->item_id = $item;
        $inventory->save();

        return response()->json(['message' => 'This code has been successfully redeemed and the item provided has been added to your inventory.']);
    }
}
