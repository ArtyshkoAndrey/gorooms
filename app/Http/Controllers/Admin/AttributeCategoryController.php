<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AttributeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $model): View
    {
        $attribute_categories = AttributeCategory::filteredByModel($model)->paginate(20);
        return view('admin.attributes_categories.index', compact('attribute_categories', 'model'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(string $model): View
    {
        return view('admin.attributes_categories.create', compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $model): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model_type' => 'required',
        ]);

        AttributeCategory::create($validated);

        return redirect()->route('admin.attributes_categories.index', $model)->with('success');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $model
     * @param  \App\Models\AttributeCategory  $attributesCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(string $model, AttributeCategory $attributesCategory): View
    {
        return view('admin.attributes_categories.edit', compact('model', 'attributesCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AttributeCategory  $attributesCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $model, AttributeCategory $attributesCategory): RedirectResponse
    {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model_type' => 'required',
        ]);

        $attributesCategory->update($validated);

        return redirect()->route('admin.attributes_categories.index', $model);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttributeCategory  $attributesCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($model, AttributeCategory $attributesCategory): RedirectResponse
    {
        $attributesCategory->delete();
        return redirect()->route('admin.attributes_categories.index', $model);
    }
}
