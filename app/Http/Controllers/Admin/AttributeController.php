<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Models\{AttributeCategory, Attribute, Hotel, Room};
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $model): View
    {
        $attributes = Attribute::filteredByModel($model)->joinCategoryName()->orderBy('category_name')->paginate(20);

        return view('admin.attributes.index', compact('attributes', 'model'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(string $model): View
    {
        $categories = AttributeCategory::filteredByModel($model)->get();

        return view('admin.attributes.create', compact('categories', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeRequest $request, string $model): RedirectResponse
    {
        $validated = $request->validated();
        $attribute = Attribute::create($validated);
        return redirect()->route('admin.attributes.index', $model)->with('success', true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function show(string $model, Attribute $attribute): View
    {
        return view('admin.attributes.show', compact('attribute', 'model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function edit(string $model, Attribute $attribute): View
    {
        $categories = AttributeCategory::filteredByModel($model)->get();

        return view('admin.attributes.edit', compact('attribute', 'categories', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeRequest $request, string $model, Attribute $attribute): RedirectResponse
    {
        $validated = $request->validated();
        $attribute->fill($validated);
        $attribute->save();
        return redirect()->route('admin.attributes.index', $model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $model, Attribute $attribute): RedirectResponse
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index', $model);
    }
}
