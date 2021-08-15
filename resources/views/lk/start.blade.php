@extends('lk.layouts.app')

@section('content')

  <div class="overlay open"></div>

  <div class="popup popup_horizontal open" id="newObject">
    {{ dump($errors) }}
    <form action="{{ route('lk.object.store') }}" method="post">
      @csrf
      <img src="{{ asset('img/lk/close.png') }}" alt="" id="close" class="close-this">
      <h2 class="title title_blue popup__title">Зарегистрировать новый объект</h2>
      <div class="d-flex align-items-center case">

        <input type="text"
               class="field"
               value="{{ auth()->check() ? old('name', auth()->user()->name) : old('name') }}"
               placeholder="ФИО"
               name="name">
        <input type="phone"
               class="field"
               value="{{ auth()->check() ? old('phone', auth()->user()->phone) : old('phone') }}"
               placeholder="Личный телефон"
               name="phone">
      </div>

      <div class="d-flex align-items-center case">
        <input type="text"
               class="field"
               value="{{ auth()->check() ? old('position', auth()->user()->position) : old('position') }}"
               placeholder="Должность"
               name="position">
        <input type="email"
               class="field"
               value="{{ auth()->check() ? old('email', auth()->user()->email) : old('email') }}"
               placeholder="Личный e-mail"
               name="email">
      </div>

      <div class="d-flex align-items-center case">
        <input type="text" class="field"
               {{ !auth()->check() ? 'required' : '' }}
               placeholder="Придумайте пароль"
               value="{{ old('password') }}"
               name="password"
        >
        <input type="text" class="field"
               placeholder="Придумайте кодовое слово"
               value="{{ auth()->check() ? old('code', auth()->user()->code) : old('code') }}"
               name="code">
      </div>


      <div class="d-flex align-items-center case">
        <input type="text"
               class="field"
               placeholder="Название объекта размещения"
               value="{{ old("hotel.name") }}"
               name="hotel[name]">

        <div class="select">
          <input type="hidden"
                 name="hotel[type]"
                 value="{{ old('hotel.type') }}"
                 id="hotel_type"
                 required>

          <div class="select__top">
            <span class="select__current">Тип объекта</span>
            <img class="select__arrow" src="{{ asset('img/lk/arrow.png') }}" alt="arrow">
          </div>
          <ul class="select__hidden">
            @foreach($types as $type)
              <li class="select__item select__item_blue {{ old('hotel.type') === $type->id ? 'active' : '' }}" data-value="{{ $type->id }}">{{ $type->name }}</li>
            @endforeach
          </ul>
        </div>
      </div>

      <div class="d-flex align-items-start case">
        <div style="width: 49%">
          <input type="text"
                 class="field w-100"
                 id="address"
                 name="address"
                 value="{{ old('address') }}"
                 placeholder="Адрес объекта">
        </div>

        <div class="choice choice_half align-items-start agreement-choice">
          <input type="checkbox" id="agreement" name="agreement">
          <div class="check">
            <div class="check__flag check__flag_blue"></div>
          </div>
          <label for="agreement">Нажимая "Зарегистрировать" Вы даете согласие на обработку персональных данных и соглашаетесь c пользовательским соглашением и политикой конфиденциальности.</label>
        </div>
      </div>

      <button class="button button_blue" type="submit" disabled id="registerObject">Зарегистрировать</button>
    </form>
  </div>

@endsection

@section('js')
  <script type="application/javascript">
    // $("*").unbind("click");
    $('.agreement-choice').bind('click', function() {
      if ($('#agreement').prop('checked')) {
        $('#registerObject').removeAttr('disabled')
      } else if (!$('#agreement').prop('checked')) {
        $('#registerObject').prop('disabled', true)
      }
    })
    let el = document.getElementsByClassName('select__item_blue')
    for(let i =0; i < el.length; i++) {
      el[i].onclick = function () {
        console.log(this.dataset.value)
        document.getElementById('hotel_type').value = this.dataset.value
      };
    }

    document.getElementById('close').onclick = function () {
      window.location.href = '{{ route('index') }}'
    }
    $("input[type='phone']").mask("+7 (999) 999 99-99");
    $('.check').bind('click', function() {

      if ($(this).siblings('input[type="checkbox"]').prop('checked')) {
        $(this).siblings('input[type="checkbox"]').prop('checked', false)

      } else {
        $(this).siblings('input[type="checkbox"]').prop('checked', true)

      }
    })
    $("#address").suggestions({
      token: "a35c9ab8625a02df0c3cab85b0bc2e9c0ea27ba4",
      type: "ADDRESS",
    });


  </script>
@endsection