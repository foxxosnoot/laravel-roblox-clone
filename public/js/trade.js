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

var id;
var routes = {};
var g1 = null;
var g2 = null;
var g3 = null;
var g4 = null;
var r1 = null;
var r2 = null;
var r3 = null;
var r4 = null;

$(() => {
    id = parseInt($('meta[name="trade-info"]').attr('data-receiver'));

    const meta = 'meta[name="routes"]';
    routes.process = $(meta).attr('data-process');

    $('#sendButton').click(function() {
        const action = 'send';
        const gCurrency = $('#givingCurrency').val();
        const rCurrency = $('#receivingCurrency').val();

        $.post(routes.process, { _token, action, id, g1, g2, g3, g4, gCurrency, r1, r2, r3, r4, rCurrency }).done(function(data) {
            if (typeof data.error !== 'undefined')
                return showError(data.error);

            window.location = data.url;
        }).fail(() => showError('Unable to send trade.'));
    });
});

function isGiving(inventoryId)
{
    return g1 == inventoryId || g2 == inventoryId || g3 == inventoryId || g4 == inventoryId;
}

function isReceiving(inventoryId)
{
    return r1 == inventoryId || r2 == inventoryId || r3 == inventoryId || r4 == inventoryId;
}

function addItem(inventoryId, type)
{
    const element = `#item_${inventoryId}`;
    const isGivingOrReceiving = (type == 'giving') ? isGiving(inventoryId) : isReceiving(inventoryId);

    if (isGivingOrReceiving) {
        if (type == 'giving') {
            if (g1 == inventoryId)
                g1 = null;
            else if (g2 == inventoryId)
                g2 = null;
            else if (g3 == inventoryId)
                g3 = null;
            else if (g4 == inventoryId)
                g4 = null;
        } else {
            if (r1 == inventoryId)
                r1 = null;
            else if (r2 == inventoryId)
                r2 = null;
            else if (r3 == inventoryId)
                r3 = null;
            else if (r4 == inventoryId)
                r4 = null;
        }

        $(element).find('img').css('border-color', 'var(--section_bg)');
    } else {
        if (type == 'giving') {
            if (!g1)
                g1 = inventoryId;
            else if (!g2)
                g2 = inventoryId;
            else if (!g3)
                g3 = inventoryId;
            else if (!g4)
                g4 = inventoryId;
            else
                return;
        } else {
            if (!r1)
                r1 = inventoryId;
            else if (!r2)
                r2 = inventoryId;
            else if (!r3)
                r3 = inventoryId;
            else if (!r4)
                r4 = inventoryId;
            else
                return;
        }

        $(element).find('img').css('border-color', 'green');
    }
}

function showError(text)
{
    $('#error #errorText').html(text);
    $('#error').modal('show');
}
