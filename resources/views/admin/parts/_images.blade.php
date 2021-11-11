<div class="form-group">
    <label for="image">Изображение</label>
    <div class="custom-file">
        <input
                type="file"
                class="custom-file-input @if(isset($autoload)) js-autoload @else js-image-preview @endif "
                @isset($autoload) data-model="{{ $model }}" data-model_id="{{ $model_id }}" @endisset
                name="image[]"
                multiple
                id="image"
                accept="image/*"
        />
        <label for="image" class="custom-file-label">Выберите изображения</label>
    </div>
</div>
<div class="row mt-2 images">
    @if($images)
        @foreach($images AS $image)
            <div class="col-12 col-md-4 mb-3" id="image_{{ $image->id }}">
                <img src="{{ asset($image->path) }}" class="img-fluid img-thumbnail"
                     @if($image->default) style="border: 2px solid black" @endif alt="">
                <div class="btn-group btn-group-sm w-100 mt-1">
                    <button type="button" class="btn btn-danger" onclick="window.deleteImage({{ $image->id }})">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        @endforeach
    @endif
</div>