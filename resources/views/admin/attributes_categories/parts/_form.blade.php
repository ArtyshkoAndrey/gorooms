<div class="form-group">
    <label for="name">Наименование</label>
    <input type="text" name="name" id="name" value="{{ old('name') ?? $attributesCategory->name ?? '' }}"
           class="form-control @error('name') is-invalid @enderror" required/>
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
@if (optional(@$attributesCategory)->id)
    <input type="hidden" name="id" value="{{ $attributesCategory->id }}">
@endif
<div class="form-group">
    <label for="model">Тип категории</label>
    <select name="model_type" id="model" class="form-control">
        <option value="">Выберите категорию</option>
        @foreach(\App\Models\AttributeCategory::TYPES AS $model => $title)
            <option value="{{ $model }}" @if(isset($attributesCategory) && $attributesCategory->model_type === $model) selected @endif>{{ $title }}</option>
        @endforeach
    </select>
</div>