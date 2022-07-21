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

$(() => {
    const onsaleUntil = $('meta[name="item-info"]').attr('data-onsale-until');
    const endTimestamp = moment.tz(onsaleUntil, 'UTC').toDate();

    $('#timer').countdown(endTimestamp, function(event) {
        var string;

        if (event.offset.totalSeconds == 0) {
            $(this).remove();
            $('[data-target="#purchaseConfirmation"]').attr('disabled', true);
        }

        if (event.offset.totalSeconds > 86400)
            string = '%-D day%!D, %-H hour%!H, %-M minute%!M, %-S second%!S:s;';
        else if (event.offset.totalSeconds > 3600)
            string = '%-H hour%!H, %-M minute%!M, %-S second%!S:s;';
        else if (event.offset.totalSeconds > 60)
            string = '%-M minute%!M, %-S second%!S:s;';
        else
            string = '%-S second%!S:s;';

        $(this).text(event.strftime(string));
    });
});
