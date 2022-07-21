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

try {
    $db = new PDO('mysql:host=' . config('DB_HOST') . ';dbname=' . config('DB_NAME'), config('DB_USER'), config('DB_PASS'));
} catch (Exception $e) {
    exit('invalid db credentials');
}

$seriousKey = request_param('seriousKey');
$type       = request_param('type');
$id         = request_param('id');

if (!$seriousKey)
    exit('provide a seriousKey');
else if ($seriousKey != config('SERIOUS_KEY'))
    exit('invalid seriousKey');
else if (!$type)
    exit('provide a type');
else if (!in_array($type, config('ALLOWED_TYPES')))
    exit('invalid type');
else if (!$id)
    exit('provide an id');
