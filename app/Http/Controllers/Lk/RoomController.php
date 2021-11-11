<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App\Http\Controllers\Lk;

use Exception;
use App\Models\Room;
use App\Models\Cost;
use App\Models\Hotel;
use App\Models\CostType;
use Illuminate\View\View;
use App\Traits\UploadImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\AttributeCategory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\LK\RoomRequest;

/**
 * Edit Room in Hotel user
 */
class RoomController extends Controller
{
  use UploadImage;

  /**
   * Edit Page.
   * Show edit page or Page checked type fond
   *
   * @return View
   */
  public function edit(): View
  {
    $hotel = Auth::user()->personal_hotel;
//    If Hotel don`t have type and zero rooms
    if (!$hotel->checked_type_fond && $hotel->rooms()->count() < 1) {
      return view('lk.room.fond', compact('hotel'));
    }

    $rooms = $hotel->rooms()->get()->sortBy('order');
    $costTypes = CostType::all();
    $attribute_categories = AttributeCategory::with(['attributes' => function ($q) {
      $q->whereModel(Room::class)->get();
    }])
      ->get();
    if ($hotel->type_fond === Hotel::ROOMS_TYPE) {
      return view('lk.room.edit-rooms', compact('hotel', 'rooms', 'costTypes', 'attribute_categories'));
    }


    return view('lk.room.edit-categories', compact('hotel', 'rooms', 'costTypes', 'attribute_categories'));
  }

  /**
   * Method Update Type fond in Hotel
   *
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function fondUpdate(Request $request): RedirectResponse
  {
    $TYPES_FOND = Hotel::TYPES_FOND;
    $request->validate([
      'fond' => 'required|in:' . implode(',', $TYPES_FOND),
    ]);
    $hotel = Auth::user()->personal_hotel;

    $hotel->type_fond = $request->get('fond');
    $hotel->checked_type_fond = true;

    $hotel->save();

    return redirect()->route('lk.room.edit')->with('success', 'Тип Фонда обновлён');
  }

  /**
   * @throws Exception
   */
  public function saveRoom(RoomRequest $request): JsonResponse
  {

    $hotel = Hotel::whereHas('rooms', function ($q) use ($request) {
      $q->where('id', $request->get('id'));
    })->firstOrFail();
    $room = Room::find($request->get('id'));

    if ($hotel->type_fond === Hotel::ROOMS_TYPE) {
      $room = $this->saveDataTypeRoom($request->all(), $room);
    }

    $room->category()->associate($request->get('category'));
    $room->costs()->delete();

    foreach ($request->get('types') as $type) {
      $cost = new Cost();
      $cost->value = $type['value'];
      $cost->period()->associate($type['data']);
      $cost->room()->associate($room->id);
      $cost->save();
    }

    $room->save();

    if ($room->wasChanged(['category_id', 'name'])) {
      $room->moderate = true;
      $room->save();
    }

    return response()->json(['success' => true, 'room' => $room]);
  }

  /**
   * Save data if hotel has type Room
   *
   * @param      $data
   * @param Room $room
   *
   * @return Room
   */
  private function saveDataTypeRoom($data, Room $room): Room
  {
    $room->name = $data['name'];
    $room->order = $data['order'];
    $room->number = $data['number'];

    return $room;
  }

  /**
   * Delete Room
   *
   * @throws Exception
   */
  public function deleteRoom(int $id): JsonResponse
  {
    $room = Room::findOrFail($id);
    $status = $room->delete();
//    TODO: deleting image;
    return response()->json(['success' => $status]);
  }

  /**
   * Create empty Room
   *
   * @param Request $request
   *
   * @return JsonResponse
   */
  public function create(Request $request): JsonResponse
  {
    $hotel = Hotel::find($request->get('hotel_id'));
    $room = new Room();
    $room->hotel()->associate($request->get('hotel_id'));

    if ($hotel->type_fond === Hotel::CATEGORIES_TYPE) {
      $count = $hotel->rooms()->count();
      if ($count === 0) {
        $room->order = 1;
      } else if ($count > 0) {
        $room->order = $hotel->rooms()->orderBy('order')->first()->order + 1;
      }
    }

    $room->moderate = true;

    $status = $room->save();

    return response()->json(['success' => $status, 'room' => $room]);
  }

  /**
   * Return all attributes in room
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function getAttributes(int $id): JsonResponse
  {

    $room = Room::findOrFail($id);

    return response()->json(['attrs' => $room->attrs]);
  }

  /**
   * Save checked attributes in room
   *
   * @param int     $id
   * @param Request $request
   *
   * @return JsonResponse
   */
  public function putAttributes(int $id, Request $request): JsonResponse
  {
    $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'required|exists:attributes,id',
    ]);

    $room = Room::findOrFail($id);
    $ids = $room->attrs()->pluck('id')->toArray();
    if (count(array_diff($request->get('ids'), $ids)) > 0) {
      $room->moderate = true;
    }

    $room->attrs()->sync($request->get('ids'));
    $room->save();

    return response()->json(['success' => true, 'room' => $room]);
  }
}