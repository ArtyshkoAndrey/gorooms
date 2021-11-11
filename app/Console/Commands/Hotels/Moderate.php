<?php

namespace App\Console\Commands\Hotels;

use Hash;
use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Console\Command;

class Moderate extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'hotels:moderate';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Set all Hotel moderate status';

  /**
   * Execute the console command.
   * Нормализация отелей
   * Все отели на модерацию
   * Все комнаты на модерацию
   * Если у теля есть комната не на модерации то show будет 1
   *
   * @return int
   */
  public function handle(): int
  {
    $hotels = Hotel::withoutGlobalScope('moderation')->get();
    foreach ($hotels as $hotel) {
      $hotel->show = false;

      $hotel->moderate = true;
      $hotel->old_moderate = false;
      $hotel->show = false;
      if ($hotel->rooms()->withoutGlobalScope('moderation')->count() > 0) {
        $hotel->old_moderate = true;
      } else {
        $hotel->old_moderate = false;
      }
      $hotel->save();

      $rooms = Room::withoutGlobalScope('moderation')->where('hotel_id', $hotel->id)->get();

      foreach ($rooms as $room) {
        $room->moderate = true;
        $room->save();
      }
    }
    return 0;
  }
}
