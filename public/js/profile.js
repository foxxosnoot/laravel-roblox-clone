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
var inventoryPublic = true;
var currentCategory = '';
var currentPage = 1;
var itemTypesWithPadding = [];
var itemTypePadding = '0px';

$(() => {
    const meta = 'meta[name="user-info"]';
    id = parseInt($(meta).attr('data-id'));
    inventoryPublic = parseInt($(meta).attr('data-inventory-public'));

    if (inventoryPublic)
        inventory('hats', 1);

    itemTypePadding = $('meta[name="item-type-padding-amount"]').attr('content');
    itemTypesWithPadding = JSON.parse($('meta[name="item-types-with-padding"]').attr('content'));

    $('[data-category]').click(function() {
        var oldCategory = currentCategory;

        $(`[data-category="${currentCategory}"]`).removeClass('active');
        $(this).addClass('active');

        currentCategory = $(this).attr('data-category');

        if (currentCategory != oldCategory)
            inventory(currentCategory, 1);
    });
});

function inventory(category, page)
{
    $.get('/api/users/inventory', { id, category, page }).done((data) => {
        $('#inventory').html('');
        currentCategory = category;
        currentPage = page;

        if (typeof data.error !== 'undefined')
            return $('#inventory').html(`<div class="col">${data.error}</div>`);

        $.each(data.items, function() {
            const padding = (itemTypesWithPadding.includes(this.type)) ? itemTypePadding : '0px';

            $('#inventory').append(`
            <div class="col-6 col-md-3">
                <div class="card" style="border:none;padding:5px;padding-top:0;padding-bottom:0;">
                    <a href="${this.url}" style="color:inherit;font-weight:600;">
                        <img style="background:var(--section_bg_inside);border-radius:6px;padding:${padding};" src="${this.thumbnail}">
                        <div class="text-truncate">${this.name}</div>
                    </a>
                </div>
            </div>`);
        });

        if (data.total_pages > 1) {
            const previousDisabled = (data.current_page == 1) ? 'disabled' : '';
            const nextDisabled = (data.current_page == data.total_pages) ? 'disabled' : '';
            const previousPage = data.current_page - 1;
            const nextPage = data.current_page + 1;

            $('#inventory').append(`
            <div class="col-12 text-center">
                <button class="btn btn-sm btn-danger" onclick="inventory('${currentCategory}', ${previousPage})" ${previousDisabled}>&laquo;</button>
                <span class="text-muted ml-2 mr-2">${data.current_page} of ${data.total_pages}</span>
                <button class="btn btn-sm btn-success" onclick="inventory('${currentCategory}', ${nextPage})" ${nextDisabled}>&raquo;</button>
            </div>`);
        }
    }).fail(() => $('#inventory').html('<div class="col">Unable to get inventory.</div>'));
}
