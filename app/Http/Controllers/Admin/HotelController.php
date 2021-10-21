<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelRequest;
use App\Models\Attribute;
use App\Models\Cost;
use App\Models\CostType;
use App\Models\Hotel;
use App\Models\HotelType;
use App\Models\Image;
use App\Models\Metro;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $hotels = Hotel::paginate(20);
        return view('admin.hotel.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $attributes = Attribute::where('model', Hotel::class)->orWhereNull('model')->get();
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

    private function save(Hotel &$hotel, HotelRequest $request)
    {
        try{
            $validated = $request->validated();

            foreach (Hotel::getFillableData($validated) AS $option => $value)
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
            foreach ($request->get('metros', []) AS $metros) {
                if (empty($metros['name'])) continue;
                $metros['hotel_id'] = $hotel->id;
                Metro::create($metros);
            }
    
            if ($validated['type_id'])
                $hotel->type()->associate($validated['type_id']);
    
            $hotel->save();
        } catch(\Exception $e){
            dd($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HotelRequest $request)
    {
        $request->validated();

        $hotel = new Hotel();
        $this->save($hotel, $request);

        return redirect()->route('admin.hotels.index')->with('success', true);
    }

    /**
     * Display the specified resource.
     *
     * @param Hotel $hotel
     * @return \Illuminate\Http\Response
     */
    public function show(Hotel $hotel): View
    {
        return view('admin.hotel.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Hotel $hotel
     * @return \Illuminate\Http\Response
     */
    public function edit(Hotel $hotel): View
    {
        $attributes = Attribute::where('model', Hotel::class)->orWhereNull('model')->get();
        $costTypes = CostType::orderBy('sort')->get();
        $hotelTypes = HotelType::orderBy('sort')->get();
        return view('admin.hotel.edit', compact('hotel', 'attributes', 'costTypes', 'hotelTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Hotel $hotel
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hotel $hotel): RedirectResponse
    {
        $hotel->delete();
        return redirect()->route('admin.hotels.index');
    }
}
