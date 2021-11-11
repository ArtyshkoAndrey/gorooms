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