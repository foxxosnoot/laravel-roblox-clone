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
    var searchDelay;

    $('#navbarSearch').keyup(function() {
        const search = $(this).val();

        if (search.length == 0) {
            $('#navbarSearchResults').hide();
            $('#navbarSearchResults').html('');
            return;
        }

        clearTimeout(searchDelay);

        searchDelay = setTimeout(() => {
            $.get('/api/search/all', { search }).done(function(data) {
                $('#navbarSearchResults').html('');
                $('#navbarSearchResults').show();

                if (typeof data.error !== 'undefined' && data.error)
                    return $('#navbarSearchResults').html(`<div class="navbar-search-error">${data.error}</div>`);

                $.each(data, function() {
                    $('#navbarSearchResults').append(`
                    <div class="navbar-search-result">
                        <a href="${this.url}">
                            <div class="row">
                                <div class="col-1">
                                    <img src="${this.image}">
                                </div>
                                <div class="col-10 align-self-center" style="font-size:18px;">${this.name}</div>
                                <div class="col-1 align-self-center text-right"><i class="fas fa-arrow-right mr-2"></i></div>
                            </div>
                        </a>
                    </div>`);
                });
            }).fail(() => $('#navbarSearchResults').html('<div class="navbar-search-error">No results found.</div>'));
        }, 500);
    });
});
