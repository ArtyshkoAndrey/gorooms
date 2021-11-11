<!-- Popups  -->

<div class="overlay"></div>
<div class="popup popup_horizontal popup_white" id="popupDetails">
  <input type="hidden" name="room_id">
  <img src="{{ asset('img/lk/close.png') }}" alt="close" class="close-this">
  <h2 class="title popup__title">Детально о номере</h2>
  <p class="caption station-caption">
    Выберите пункты наиболее явно отражающие преимущества Вашего объекта. (минимум 3, максимум 9 пунктов)
  </p>
  <ul class="details">
    @foreach($attribute_categories as $category)
      <li class="detail">
        <p class="text-bold_small details__title">{{ $category->name }}</p>

        @foreach($category->attributes as $attr)
          <div class="choice">
            <input type="checkbox" data-placeholder="{{ $attr->name }}" id="attr-{{$attr->id}}" value="{{ $attr->id }}" name="attr[{{$attr->id}}]" >
            <div class="check">
              <div class="check__flag"></div>
            </div>
            <label for="attr-{{$attr->id}}">{{ $attr->name }}</label>
          </div>
        @endforeach
      </li>

    @endforeach

  </ul>
  <button type="button" class="button popup__bottom popup__button_attributes">Сохранить</button>
</div>