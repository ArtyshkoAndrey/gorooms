<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App\Http\Controllers\Moderator;

use App\User;
use App\Models\Hotel;
use App\Models\Metro;
use Illuminate\View\View;
use App\Models\Attribute;
use App\Models\HotelType;
use Illuminate\Http\Request;
use App\Models\AttributeCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Http\Requests\LK\ObjectUpdateRequest;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Foundation\Application;
use App\Notifications\NotificationPublishedHotel;
use App\Notifications\NotificationUnPublishedHotel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Rewrite Hotel
 */
class ObjectController extends Controller
{

  /**
   * @param int $id
   *
   * @return Application|Factory|View|void
   * @throws ModelNotFoundException
   */
  public function edit (int $id)
  {
    try {
      $hotel = Hotel::findOrFail($id);

      $attributes = Attribute::forHotels()->get();
      $hotelTypes = HotelType::orderBy('sort')
        ->get();
      $attributeCategories = AttributeCategory::forHotels()->get();

      return view('moderator.object.edit', compact('hotel', 'attributes', 'attributeCategories', 'hotelTypes'));
    } catch (ModelNotFoundException $e) {
      abort('404', 'Отель с таким индексом не найден');
    }
  }

  /**
   * @param int                 $id
   * @param ObjectUpdateRequest $request
   *
   * @return RedirectResponse
   */
  public function update(int $id, ObjectUpdateRequest $request): RedirectResponse
  {
    $hotel = Hotel::findOrFail($id);
    $type = $request->get('type_update');

    if ($hotel) {
      if ($type === 'phone' || $type === 'description') {

        if($request->exists('type')) {
          $hotel->type()->associate($request->get('type'));
          $hotel->save();
        }
        $hotel->update($request->all());
      } else if ($type === 'address') {
        $hotel->saveAddress($request->get('value'), $request->get('comment'));
      } else if ($type === 'metros') {
        $hotel->metros()->delete();
        $distance = $request->get('metros_time');
        $color = $request->get('metros_color');
        foreach ($request->get('metros', []) as $index => $metro) {
          $metros = [
            'name'     => $metro,
            'hotel_id' => $hotel->id,
            'distance' => $distance[$index],
            'color'    => $color[$index]
          ];
          Metro::create($metros);
        }
      } else if ($type === 'attr') {
        $attr = [];
        foreach ($request->get('attr') as $item => $value) {
          if ($value === 'true') {
            $attr[] = $item;
          }
        }

        $hotel->attrs()->sync($attr);
      }
    }

    return redirect()->back()->with('success', 'Данные отеля обновились');
  }

  public function upload (int $id): RedirectResponse
  {
    $hotel = Hotel::findOrFail($id);

    $roomsCount = $hotel->rooms()->where('moderate' , false)->count();
    if ($roomsCount <=0) {
      return redirect()->back()->with('error', 'У отеля нет опубликованных комнат');
    }

    $hotel->moderate = false;
    $hotel->show = true;

    $hotel->save();

    $users = $hotel->users()->wherePivot('hotel_position', User::POSITION_GENERAL)->get();

    Notification::send($users, new NotificationPublishedHotel($hotel));

    return redirect()->back()->with('success', 'Отель опубликован');
  }

  public function unupload (int $id): RedirectResponse
  {
    $hotel = Hotel::findOrFail($id);
    $hotel->moderate = false;
    $hotel->save();
    $users = $hotel->users()->wherePivot('hotel_position', User::POSITION_GENERAL)->get();

    Notification::send($users, new NotificationUnPublishedHotel($hotel));

    return redirect()->back()->with('success', 'Уведомления отправлено на почтовый ящик пользователей');
  }

}