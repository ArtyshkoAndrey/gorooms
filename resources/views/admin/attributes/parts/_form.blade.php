<div class="form-group">
    <label for="name">Наименование</label>
    <input type="text" name="name" id="name" value="{{ old('name') ?? @$attribute->name ?? '' }}"
           class="form-control @error('name') is-invalid @enderror" required/>
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
@if (optional(@$attribute)->id)
    <input type="hidden" name="id" value="{{ $attribute->id }}">
@endif
<div class="form-group">
    <label for="model">Категория</label>
    <select name="attribute_category_id" class="form-control">
        <option value="">Выберите категорию</option>
        @foreach($categories AS $model)
            <option value="{{ $model->id }}" @if(isset($attribute) && $attribute->attribute_category_id === $model->id) selected @endif>{{ $model->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <div class="form-check form-check-inline">
        <input type="hidden" name="in_filter" value="0">
        <input class="form-check-input" type="checkbox" id="in_filter" value="1" name="in_filter"
               @if(@$attribute->in_filter || old('in_filter', 0) === 1)
               checked
                @endif
        >
        <label class="form-check-label" for="in_filter">Участвует в фильтре</label>
    </div>
</div>
<div class="form-group">
    <label for="description">Описание</label>
    <textarea name="description" id="description"
              class="form-control @error('description') is-invalid @enderror">{{ old('description') ?? @$hotel->description ?? '' }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

