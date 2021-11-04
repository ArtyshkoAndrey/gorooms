<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Attribute;
use App\Models\CostType;
use App\Models\Hotel;
use App\Models\Image;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Cost;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::all();
        return view('admin.room.index', compact('rooms'));
    }

    public function create(Hotel $hotel): View
    {
        $attributes = Attribute::forRooms()->get();
        $costTypes = CostType::orderBy('sort')->get();
        return view('admin.room.create', compact('hotel', 'attributes', 'costTypes'));
    }

    public function save(Room &$room, RoomRequest $request): void
    {
        $validated = $request->validated();


        $room->name = $validated['name'];
        $room->description = $validated['description'];
        $room->hotel_id = $validated['hotel_id'];
        $room->is_hot = $request->has('is_hot');
        $room->save();
        $room->attachMeta($request);
        if ($request->has('category_id'))
            $room->category()->associate($validated['category_id']);
        if ($request->has('attributes'))
            $room->attrs()->sync($validated['attributes']);
		$room->costs()->delete();
        foreach ($validated['cost'] as $cost) {
        	$c = new Cost();
		    $c->value = $cost['value'];
		    $c->period()->associate($cost['period']);
		    $c->room()->associate($room->id);
		    $c->save();
        }
        Image::upload($request, $room);
        $room->save();
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $room = new Room();
        $this->save($room, $request);

        return redirect()->route('admin.hotels.show', $room->hotel->slug);
    }

    public function show(Room $room): View
    {
        return view('admin.room.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $attributes = Attribute::where('model', Room::class)->orWhereNull('model')->get();
        $costTypes = CostType::orderBy('sort')->get();
        return view('admin.room.edit', compact('room', 'attributes', 'costTypes'));
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {

        $this->save($room, $request);

        return redirect()->route('admin.hotels.show', $room->hotel->slug)->with('success', true);
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route('admin.hotels.show', $room->hotel->slug);
    }
}
