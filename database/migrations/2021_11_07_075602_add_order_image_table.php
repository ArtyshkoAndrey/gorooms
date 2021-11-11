<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 *  Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Image;

class AddOrderImageTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::table('images', function (Blueprint $table) {
      $table->dropColumn(['default']);

      $table->tinyInteger('order')->nullable();
    });


    $hotels = \App\Models\Hotel::withoutGlobalScope('moderation')->get();
    foreach ($hotels as $h) {
      $i = 1;
      $images = $h->images()->each(function (Image $image) use (&$i) {
        $image->order = $i;
        $image->save();

        $i++;
      });
    }

    $rooms = \App\Models\Room::withoutGlobalScope('moderation')->get();
    foreach ($rooms as $r) {
      $i = 1;
      $images = $r->images()->each(function (Image $image) use (&$i) {
        $image->order = $i;
        $image->save();

        $i++;
      });
    }

    Image::whereNull('order')->delete();
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::table('images', function (Blueprint $table) {
      $table->boolean('default')->default(0);

      $table->dropColumn(['order']);
    });
  }
}
