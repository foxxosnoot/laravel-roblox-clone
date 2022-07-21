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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('receiver_id')->unsigned();
            $table->bigInteger('sender_id')->unsigned();
            $table->string('status')->default('pending'); // pending, declined, accepted
            $table->bigInteger('giving_1')->unsigned()->nullable();
            $table->bigInteger('giving_2')->unsigned()->nullable();
            $table->bigInteger('giving_3')->unsigned()->nullable();
            $table->bigInteger('giving_4')->unsigned()->nullable();
            $table->integer('giving_currency')->nullable();
            $table->bigInteger('receiving_1')->unsigned()->nullable();
            $table->bigInteger('receiving_2')->unsigned()->nullable();
            $table->bigInteger('receiving_3')->unsigned()->nullable();
            $table->bigInteger('receiving_4')->unsigned()->nullable();
            $table->integer('receiving_currency')->nullable();
            $table->timestamps();

            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('giving_1')->references('id')->on('inventories')->onDelete('set null');
            $table->foreign('giving_2')->references('id')->on('inventories')->onDelete('set null');
            $table->foreign('giving_3')->references('id')->on('inventories')->onDelete('set null');
            $table->foreign('giving_4')->references('id')->on('inventories')->onDelete('set null');
            $table->foreign('receiving_1')->references('id')->on('inventories')->onDelete('set null');
            $table->foreign('receiving_2')->references('id')->on('inventories')->onDelete('set null');
            $table->foreign('receiving_3')->references('id')->on('inventories')->onDelete('set null');
            $table->foreign('receiving_4')->references('id')->on('inventories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
}
