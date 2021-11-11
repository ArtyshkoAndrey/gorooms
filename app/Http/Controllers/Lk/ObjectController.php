<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
use App\Http\Requests\LK\ObjectUpdateRequest;
use App\Models\Attribute;
use App\Models\AttributeCategory;
use App\Models\Hotel;
use App\Models\HotelType;
use App\Models\Metro;
use App\Traits\UploadImage;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * edit Hotel
 */
class ObjectController extends Controller
{
  use UploadImage;
  /**
   * Store a newly created resource in storage.
   *
   * @param Request $request
   * @return RedirectResponse
   */
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'name' => 'required|string|min:0',
      'password' => 'string|min:3',
      'position' => 'required|string|min:3',
      'code' => 'required|string|min:3',
      'phone' => 'required|string',
      'email' => 'required|string',
      'hotel' => 'required|array',
      'hotel.name' => 'required|string',
      'hotel.type' => 'required|exists:hotel_types,id',
      'address' => 'required|string'
    ]);
    if (auth()->check()) {
      $request->validate([
        'email' => 'unique:users,email,' . auth()->id(),
      ]);
      $user = User::find(auth()->id());
      $user->update($request->all());
      $user->password = Hash::make($request->password);
      $user->save();
    } else {
      $request->validate([
        'email' => 'unique:users,email'
      ]);

      $user = new User($request->all());
      $user->password = Hash::make($request->password);
      $user->save();
      Auth::loginUsingId($user->id, true);
    }

    $hotel = new Hotel();
    $hotel->name = $request->get('hotel')['name'];
    $hotel->type()->associate($request->get('hotel')['type']);
    $hotel->user()->associate($user->id);
    $hotel->save();

    $hotel->users()->attach($user->id, ['hotel_position' => User::POSITION_GENERAL]);

    $hotel->saveAddress($request->get('address', ''));
    return redirect()->route('lk.object.edit');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @return Application|Factory|View
   */
  public function edit()
  {
    $hotel = auth()->user()->personal_hotel;
    $attributes = Attribute::where('model', Hotel::class)
      ->orWhereNull('model')->get();
    $hotelTypes = HotelType::orderBy('sort')
      ->get();
    $attributeCategories = AttributeCategory::with(['attributes' => function ($q) {
        $q->whereModel(Hotel::class)->get();
      }])
      ->get();
    return view('lk.object.edit',
      compact('hotel',
        'attributeCategories',
        'attributes',
        'hotelTypes')
    );
  }

  /**
   * Update the specified resource in storage.
   *
   * @param ObjectUpdateRequest $request
   * @return RedirectResponse
   */
  public function update(ObjectUpdateRequest $request): RedirectResponse
  {

    $hotel = auth()->user()->personal_hotel;
    $type = $request->get('type_update');

    if ($hotel) {
      if ($type === 'attr') {
        $attr = [];
        foreach ($request->get('attr') as $item => $value) {
          if ($value === 'true') {
            $attr[] = $item;
          }
        }

        $hotel->attrs()->sync($attr);
      } else if ($type === 'phone' || $type === 'description') {

        $hotel->update($request->all());

      } else if ($type === 'address') {
        $hotel->address()->update($request->except(['type_update', '_token']));
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
      } else {
        return redirect()->back()->with('error', 'Не верные данные');
      }
    }
    return redirect()->back()->with('success', 'Данные сохранены');
  }
}
