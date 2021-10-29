<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Models\{AttributeCategory, Attribute, Hotel, Room};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $attributes = Attribute::paginate(20);

        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $hotel_categories = AttributeCategory::where("model_type", Hotel::class)->get();
        $room_categories = AttributeCategory::where("model_type", Room::class)->get();

        return view('admin.attributes.create', compact('hotel_categories', 'room_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $attribute = Attribute::create($validated);
        return redirect()->route('admin.attributes.index', $attribute->category)->with('success', true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function show(Attribute $attribute): View
    {
        return view('admin.attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attribute): View
    {
        $hotel_categories = AttributeCategory::where("model_type", Hotel::class)->get();
        $room_categories = AttributeCategory::where("model_type", Room::class)->get();

        return view('admin.attributes.edit', compact('attribute', 'hotel_categories', 'room_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeRequest $request, Attribute $attribute): RedirectResponse
    {
        $validated = $request->validated();
        $attribute->fill($validated);
        $attribute->save();
        return redirect()->route('admin.attributes.index', $attribute->category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute): RedirectResponse
    {
        $category = $attribute->category;
        $attribute->delete();
        return redirect()->route('admin.attributes.index', $category);
    }
}
