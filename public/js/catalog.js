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

var currentCategory = '';
var currentSearch = '';
var itemTypesWithPadding = [];
var itemTypePadding = '0px';

$(() => {
    itemTypePadding = $('meta[name="item-type-padding-amount"]').attr('content');
    itemTypesWithPadding = JSON.parse($('meta[name="item-types-with-padding"]').attr('content'));

    $('#search').submit(function(event) {
        event.preventDefault();

        var oldSearch = currentSearch;
        currentSearch = $(this).find('input').val();

        if (currentSearch != oldSearch)
            search(currentCategory, 1, currentSearch);
    });

    $('[data-category]').click(function() {
        var oldCategory = currentCategory;

        $(`[data-category="${currentCategory}"]`).removeClass('active');
        $(this).addClass('active');

        currentCategory = $(this).attr('data-category');

        if (currentCategory != oldCategory)
            search(currentCategory, 1, currentSearch);
    });
    
    search('hats', 1, currentSearch);
});

function search(category, page, search)
{
    $.get('/api/catalog/search', { category, page, search }).done((data) => {
        $('#items').html('');
        currentCategory = category;
        currentSearch = search;

        if (typeof data.error !== 'undefined')
            return $('#items').html(`<div class="col">${data.error}</div>`);

        $.each(data.items, function() {
            var price = `<span><i class="currency"></i> ${this.price}</span>`;
            var header = '';
            const padding = (itemTypesWithPadding.includes(this.type)) ? itemTypePadding : '0px';

            if (this.onsale && this.price == 0)
                price = `<span class="text-success">Free</span>`;
            else if (!this.onsale)
                price = `<span class="text-muted">Off Sale</span>`;

            if (this.limited) {
                header = `
                <div class="bg-primary text-white text-center" style="border-radius:50%;width:30px;height:30px;position:absolute;margin-left:5px;margin-top:5px;">
                    <span style="font-size:20px;font-weight:600;margin-top:7px;">C</span>
                </div>`;
            } else if (this.timed) {
                header = `
                <div class="bg-danger text-white text-center" style="border-radius:50%;width:30px;height:30px;position:absolute;margin-left:5px;margin-top:5px;">
                    <span style="font-size:17px;font-weight:600;"><i class="fas fa-clock" style="margin-top:6.5px;"></i></span>
                </div>`;
            }

            $('#items').append(`
            <div class="col-6 col-md-3" style="font-weight:600;">
                <div class="card">
                    <div class="card-body">
                        <a href="${this.url}">
                            ${header}
                            <img style="background:var(--section_bg_inside);border-radius:6px;padding:${padding};" src="${this.thumbnail}">
                        </a>
                        <div class="row">
                            <div class="col-md-2 align-self-center hide-sm">
                                <a href="${this.url}">
                                    <img style="background:var(--headshot_bg);border-radius:50%;max-width:290%;" src="${this.creator.image}">
                                </a>
                            </div>
                            <div class="col-md-10">
                                <div class="text-truncate">
                                    <a href="${this.url}" style="color:inherit;">${this.name}</a>
                                    <div class="text-muted" style="margin-top:-5px;">By: <a href="${this.creator.url}">${this.creator.username}</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">${price}</div>
                    </div>
                </div>
            </div>`);
        });

        if (data.total_pages > 1) {
            const previousDisabled = (data.current_page == 1) ? 'disabled' : '';
            const nextDisabled = (data.current_page == data.total_pages) ? 'disabled' : '';
            const previousPage = data.current_page - 1;
            const nextPage = data.current_page + 1;

            $('#items').append(`
            <div class="col-12 text-center">
                <button class="btn btn-sm btn-danger" onclick="search('${currentCategory}', ${previousPage}, '${currentSearch}')" ${previousDisabled}>&laquo;</button>
                <span class="text-muted ml-2 mr-2">${data.current_page} of ${data.total_pages}</span>
                <button class="btn btn-sm btn-success" onclick="search('${currentCategory}', ${nextPage}, '${currentSearch}')" ${nextDisabled}>&raquo;</button>
            </div>`);
        }
    }).fail(() => $('#items').html('<div class="col">Unable to get items.</div>'));;
}
