<div class="form-group">
    <label for="name">Наименование</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror "
           value="{{ old('name') ?? @$room->name ?? '' }}" name="name" id="name"/>
</div>
<div class="form-group">
    <label for="description">Описание</label>
    <textarea type="text" class="form-control editor " name="description"
              id="description">{{ old('description') ?? @$room->description ?? '' }}</textarea>
</div>
<div class="form-group">
    <label for="meta_title">Тайтл</label>
    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') ?? @$room->meta_title ?? '' }}" class="form-control "/>
</div>
<div class="form-group">
    <label for="meta_description">Мета описание</label>
    <textarea name="meta_description" id="meta_description"
              class="form-control  ">{{ old('meta_description') ?? @$room->meta_description ?? '' }}</textarea>
</div>
<div class="form-group">
    <label for="meta_keywords">Мета ключи</label>
    <textarea name="meta_keywords" id="meta_keywords"
              class="form-control  ">{{ old('meta_keywords') ?? @$room->meta_keywords ?? '' }}</textarea>
</div>
<div class="form-group">
    <label class="h5 w-100">Детально о номере</label>
    @foreach($attributes AS $attribute)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="attr_{{ $loop->index }}" value="{{ $attribute->id }}"
                   name="attributes[{{$attribute->id}}}]"
                   @if((isset($room) && $room->attrs()->count() > 0 && $room->attrs->contains($attribute)) || old('attributes.'.$attribute->id, 0) == $attribute->id)
                   checked
                    @endif
            >
            <label class="form-check-label" for="attr_{{ $loop->index }}">{{ $attribute->name }}</label>
        </div>
    @endforeach
</div>
@if($hotel->categories()->count() > 0)
    <div class="form-group">
        <label for="category">Категория</label>
        <select name="category_id" id="category" class="form-control">
            <option value="">Без категории</option>
            @foreach($hotel->categories AS $category)
                <option value="{{ $category->id }}"
                        @if ((isset($room) && optional($room)->category && $room->category->id === $category->id) || old('category_id') == $category->id)
                selected
                        @endif>{{$category->name}}</option>
            @endforeach
        </select>
    </div>
@endif

<div class="form-group">
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="is_hot" value="1"
               name="is_hot"
               @if((isset($room) && $room->is_hot) || old('is_hot', 0) == 1)
               checked
                @endif
        >
        <label class="form-check-label" for="is_hot">Горячее предложение</label>
    </div>
</div>

<div class="dropdown-divider"></div>
<div class="h4">Цены</div>
@foreach ($costTypes as $costType)
	@if(isset($room))
		@php
		    $id = $costType->id;
		    $costRoom = $room->costs()->whereHas('period', function ($q) use($id) {
		      $q->where('cost_type_id', $id);
		    })->first();
	    @endphp
    @endif
    <div class="form-row">
        <div class="form-group col-12 col-md-6">
            <label for="cost_{{$costType->id}}">Цена "{{ $costType->name }}"</label>
            <input type="number" step="0.01" class="form-control @error('cost.'.$costType->id.'.value') is-invalid @enderror" name="cost[{{ $costType->id }}][value]"
                   value="{{ old('cost.'.$costType->id.'.value') ?? (isset($room) ? $costRoom->value ?? '' : '') ?? '' }}">
        </div>
        <div class="form-group col-12 col-md-6">
        	<label for="cost_{{$costType->id}}">Период</label>
        	<select class="form-control" name="cost[{{ $costType->id }}][period]">
                @if(isset($room))
        		    @foreach($costType->periods as $period)
                        <option value="{{ $period->id }}" {{ $costRoom ? $costRoom->period->id === $period->id ? 'selected' : '' : ''  }}>{{ $period->info }}</option>
                    @endforeach
                @endif
        	</select>
        </div>
        <input type="hidden" name="cost[{{ $costType->id }}][type_id]" value="{{$costType->id}}">
    </div>
@endforeach
