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

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CreatorAreaController extends Controller
{
    public function renderPreview(Request $request)
    {
        if (!config('site.renderer.previews_enabled'))
            abort(404);

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'dimensions:min_width=1024,max_height=1024,min_height=1024,max_height=1024', 'mimes:png,jpg,jpeg', 'max:2048']
        ]);

        if (!in_array($request->type, ['shirt', 'pants']) || $validator->fails())
            return response()->json(['error' => 'Invalid file.']);

        Storage::putFileAs('uploads', $request->file('file'), "preview_{$request->type}.png");

        $data = render($request->type, 'preview');

        return response()->json(['thumbnail' => $data]);
    }
}
