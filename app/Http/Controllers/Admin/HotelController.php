<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Hotel;
use App\Models\Image;
use App\Models\Metro;
use App\Models\CostType;
use App\Models\Attribute;
use App\Models\HotelType;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use App\Http\Requests\HotelRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $search = '';

        if($request->has('search')) {
            $search = $request->input('search');

            $hotels = Hotel::query()->where('name', 'LIKE', "%{$search}%")->paginate(20);
        } else {
            $hotels = Hotel::paginate(20);
        }
        
        return view('admin.hotel.index', compact('hotels', 'search'));
    }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    $attributes = Attribute::forHotels()->get();
    $costTypes = CostType::orderBy('sort')->get();
    $hotelTypes = HotelType::orderBy('sort')->get();
    return view('admin.hotel.create', compact('attributes', 'costTypes', 'hotelTypes'));
  }

  public function block(Hotel $hotel)
  {
    $hotel->block();

    return redirect()->back()->with('message', __('messages.hotel_blocked', ['name' => $hotel->name]));
  }

  public function unblock(Hotel $hotel)
  {
    $hotel->unblock();

    return redirect()->back()->with('message', __('messages.hotel_unblocked', ['name' => $hotel->name]));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function store(HotelRequest $request)
  {
    $request->validated();

    $hotel = new Hotel();
    $this->save($hotel, $request);

    return redirect()->route('admin.hotels.index')->with('success', true);
  }

  private function save(Hotel &$hotel, HotelRequest $request)
  {
    try {
      $validated = $request->validated();

      foreach (Hotel::getFillableData($validated) as $option => $value)
        $hotel->$option = $value;

      $hotel->save();

      if ($request->has('attributes'))
        $hotel->attrs()->sync($validated['attributes']);

      if ($request->hasFile('image'))
        Image::upload($request, $hotel);

      if ($request->has('address'))
        $hotel->saveAddress($request->get('address', ''));

      if ($request->has('address'))
        $hotel->saveAddress($request->get('address'), $request->get('address_comment', ''));


      $hotel->attachMeta($request);


      $hotel->metros()->delete();
      foreach ($request->get('metros', []) as $metros) {
        if (empty($metros['name'])) continue;
        $metros['hotel_id'] = $hotel->id;
        Metro::create($metros);
      }

      if ($validated['type_id'])
        $hotel->type()->associate($validated['type_id']);

      $hotel->save();
    } catch (Exception $e) {
      dd($e);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param Hotel $hotel
   *
   * @return Response
   */
  public function show(Hotel $hotel): View
  {
    return view('admin.hotel.show', compact('hotel'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Hotel $hotel
   *
   * @return Response
   */
  public function edit(Hotel $hotel): View
  {
    $attributes = Attribute::forHotels()->get();
    $costTypes = CostType::orderBy('sort')->get();
    $hotelTypes = HotelType::orderBy('sort')->get();
    return view('admin.hotel.edit', compact('hotel', 'attributes', 'costTypes', 'hotelTypes'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param Request $request
   * @param Hotel                    $hotel
   *
   * @return Response
   */
  public function update(HotelRequest $request, Hotel $hotel): RedirectResponse
  {
    $this->save($hotel, $request);
    return redirect()->route('admin.hotels.show', $hotel);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Hotel $hotel
   *
   * @return RedirectResponse
   * @throws Exception
   */
  public function destroy(Hotel $hotel): RedirectResponse
  {
    $users = $hotel->users;

    $hotel->users()->detach();

    $hotel->delete();

    foreach ($users as $user) {
      $user->forceDelete();
    }
    return redirect()->route('admin.hotels.index');
  }
}
