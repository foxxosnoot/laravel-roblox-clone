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

use PaypalIPN;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UpgradeController extends Controller
{
    public function index()
    {
        $products = config('products');
        $image = config('badges')[9]['image'];
        $image = asset("img/badges/{$image}.png");

        return view('web.account.upgrade.index')->with([
            'products' => $products,
            'image' => $image
        ]);
    }

    public function checkout($product)
    {
        $isCurrency = $product != 'membership';
        $products = ($isCurrency) ? config('products.currency') : config('products');

        if ($product == 'currency' || str_contains($product, '_') || !array_key_exists(str_replace('-', '_', $product), $products)) abort(404);

        $_product = $products[str_replace('-', '_', $product)];
        $paypalProduct = $products[str_replace('-', '_', $product)];

        if ($product == 'membership') {
            $_product['display_name'] = config('site.membership_name');
            $_product['display_name'] = "{$_product['display_name']} Membership";
            $_product['image'] = config('badges')[9]['image'];
            $_product['image'] = asset("img/badges/{$_product['image']}.png");
            $_product['price'] = "{$_product['price']}/month";
        } else {
            $_product['display_name'] = "{$_product['display_name']} Currency";
        }

        $_product['name'] = $product;

        $product = $_product;

        return view('web.account.upgrade.checkout')->with([
            'product' => $product,
            'paypalProduct' => $paypalProduct
        ]);
    }

    public function thankYou()
    {
        return view('web.account.upgrade.thank_you');
    }

    public function canceled()
    {
        return view('web.account.upgrade.canceled');
    }

    public function notify(Request $request)
    {
        $userId = (int) $request->custom;
        $user = User::where('id', '=', $userId);
        $isCurrency = $request->item_name != 'membership';
        $products = ($isCurrency) ? config('products.currency') : config('products');

        try {
            $ipn = new PaypalIPN;
            $verified = $ipn->verify();
        } catch (\Exception $err) {
            $verified = false;
        }

        if (
            !$verified ||
            !$user->exists() ||
            $request->item_name == 'currency' ||
            !array_key_exists($request->item_name, $products) ||
            $request->mc_gross != $products[$request->item_name]['price'] ||
            $request->mc_currency != 'USD' ||
            $request->receiver_email != config('site.paypal_email')
        ) return;

        $product = $products[$request->item_name];
        $user = $user->first();
        $user->timestamps = false;

        if ($request->payment_status == 'Completed') {
            if (!$user->ownsBadge(8))
                $user->giveBadge(8);

            if (config('site.donator_item_id')) {
                $owns = $user->ownsItem(config('site.donator_item_id'));

                if (!$owns) {
                    $inventory = new Inventory;
                    $inventory->user_id = $user->id;
                    $inventory->item_id = config('site.donator_item_id');
                    $inventory->save();
                }
            }

            if (!$isCurrency && config('site.membership_item_id')) {
                $owns = $user->ownsItem(config('site.membership_item_id'));

                if (!$owns) {
                    $inventory = new Inventory;
                    $inventory->user_id = $user->id;
                    $inventory->item_id = config('site.membership_item_id');
                    $inventory->save();
                }
            }

            if ($isCurrency) {
                $user->currency += $product['amount'];
            } else {
                $user->membership_until = Carbon::now()->addMonths(1)->toDateTimeString();
                $user->giveBadge(9);
            }

            $user->save();

            $purchase = new Purchase;
            $purchase->user_id = $user->id;
            $purchase->email = $request->payer_email;
            $purchase->product = $product['item_name'];
            $purchase->cost = $request->mc_gross;
            $purchase->save();
        }
    }
}
