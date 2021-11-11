<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App\Observers;

use Cache;
use App\Models\Hotel;

/**
 *
 */
class HotelObserver
{
  /**
   * Handle the Category "created" event.
   *
   * @param Hotel $hotel
   * @return void
   */
  public function created(Hotel $hotel): void
  {
    Cache::flush();

    $hotel->moderate = true;
    $hotel->old_moderate = false;
    $hotel->show = false;
    $hotel->save();
  }

  /**
   * Handle the Category "updated" event.
   *
   * @param Hotel $hotel
   * @return void
   */
  public function updated(Hotel $hotel): void
  {
    Cache::flush();
  }

  /**
   * Handle the Category "deleting" event.
   *
   * @param Hotel $hotel
   * @return void
   */
  public function deleting(Hotel $hotel): void
  {

  }

  /**
   * Handle the Category "deleted" event.
   *
   * @param Hotel $hotel
   * @return void
   */
  public function deleted(Hotel $hotel): void
  {
    Cache::flush();
  }

  /**
   * Handle the Category "restored" event.
   *
   * @param Hotel $hotel
   * @return void
   */
  public function restored(Hotel $hotel): void
  {
    //
  }

  /**
   * Handle the Category "force deleted" event.
   *
   * @param Hotel $hotel
   * @return void
   */
  public function forceDeleted(Hotel $hotel): void
  {
    //
  }
}
