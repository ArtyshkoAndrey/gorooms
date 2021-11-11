@extends('moderator.layouts.app')

@section('content')
  {{-- Phone --}}
  <section class="gray part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_blue">Объект</h2>
        </div>
      </div>
      <div class="row part__middle justify-content-between">
        <div class="col-auto">
          <p class="heading">Название: {{ $hotel->name }}</p>
        </div>
        <div class="col-auto">
          <p class="{{ $hotel->moderate ? 'text-danger' : 'text-success' }}">{{ $hotel->moderate ? 'На проверке' : 'Проверен' }}</p>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <p class="heading">Тип Объекта размещения: {{ $hotel->type->name }}</p>
        </div>
      </div>
      <div class="row part__content">
        <div class="col-4">
          <input type="phone" class="field" value="{{ $hotel->phone }}" placeholder="Телефон 1 объекта" disabled>
        </div>
        <div class="col-4">
          <input type="phone" class="field" value="{{ $hotel->phone_2 }}" placeholder="Телефон 2 объекта" disabled>
        </div>
        <div class="col-4">
          <input type="email" class="field" value="{{ $hotel->email }}" placeholder="Email объекта для бронирований" disabled>
        </div>
      </div>
      <div class="row part__bottom">
        <div class="col-12">
          <button class="button button_blue" id="objectBtn">Редактировать</button>
        </div>
      </div>
    </div>
  </section>

  {{-- Address --}}
  <section class="part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_blue">Адрес</h2>
        </div>
      </div>
      <div class="row part__middle">
        <div class="col-8">
          <p class="caption">Вы можете указать дополнительную информацию к адресу, чтобы посетителям было понятнее.
            Например: «Вход через арку» или «2й этаж, направо»</p>
        </div>
      </div>
      <div class="row part__middle">
        <div class="col-12">
          <div class="d-flex align-items-center">
            <p class="text-bold adress">Адрес объекта: </p>
            <p class="text">
              {{ $hotel->address->value }}
            </p>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-12">
          <div class="d-flex align-items-end">
            <p class="text-bold adress">Комментарий к адресу: </p>
            <input class="bordered comment-field field" placeholder="Комментарий" value="{{ $hotel->address->comment }}" disabled>
          </div>
        </div>



      </div>


      <div class="row part__bottom">
        <div class="col-12">
          <button class="button button_blue" id="adressBtn">Редактировать</button>

        </div>
      </div>
    </div>
  </section>

  {{-- Desciption --}}
  <section class="part gray ck-editor_hidden ck-editor__show">
  <div class="container">
    <div class="row part__top">
      <div class="col-12">
        <h2 class="title title_blue">Информация об отеле</h2>
      </div>
    </div>
    <div class="row part__middle">
      <div class="col-11">
        <p class="caption">Расскажите подробнее о Вашем объекте, его преимущества, местоположение, дополнительный сервис для гостей. Текст должен быть уникальным и содержать не менее 1500 знаков. Заполнив данное поле вы увеличиваете вероятность просмотров
          Вашего объекта до 20%.</p>
      </div>
    </div>

    <form action="{{ route('moderator.object.update', $hotel->id) }}" method="post">
      @csrf
      <input type="hidden" value="phone" name="type_update">
      <div class="row part__content">
        <div class="col-12">
          <div class="text editor__text">
            {!! $hotel->description !!}
          </div>
          <textarea id="editor2" rows="8" class="h-auto field form-control" name="description" placeholder="Введите текст">
            {{ $hotel->description }}
          </textarea>
        </div>
      </div>
      <div class="row part__bottom">
        <div class="col-12">
          <button class="button button_blue" type="button" id="howRideBtn2">Редактировать</button>

        </div>
      </div>

    </form>
  </div>
</section>

  {{-- Как добраться --}}
  <section class="part ck-editor_hidden">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_blue">Как добраться</h2>
        </div>
      </div>

      <form action="{{ route('moderator.object.update', $hotel->id) }}" method="post">
        @csrf
        <input type="hidden" value="phone" name="type_update">
        <div class="row part__content">
          <div class="col-12">
            <div class="text editor__text">
              {!! $hotel->route !!}
            </div>
            <textarea id="editor" rows="8" class="h-auto field form-control" name="route" placeholder="Введите текст">
              {{ $hotel->route }}
            </textarea>
          </div>
        </div>
        <div class="row part__bottom">
          <div class="col-12">
            <button class="button button_blue" type="button" id="howRideBtn">Редактировать</button>

          </div>
        </div>

      </form>
    </div>
  </section>

  <section class="part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_blue">Ближайшие станции метро</h2>
        </div>
      </div>
      <div class="row part__middle">
        <div class="col-12">
          <p class="caption">Выберите ближайшие станции метро к Вашему объекту размещения и укажите за какое время можно
            добраться до объекта пешком в минутах.</p>
        </div>
      </div>
      <form action="{{ route('moderator.object.update', $hotel->id) }}" method="POST">
        @csrf
        <input type="hidden" value="metros" name="type_update">
        <div id="metro" class="row part__content">
          @forelse($hotel->metros as $index => $m)
            <div class="col-12" data-id="{{ $m->id }}">
              <div class="d-flex align-items-center station">
                <div class="select" style="width: 45%">
                  <select name="metros[]"
                          class="form-control field metros w-100"
                          required>
                    <option value="{{ $m->name }}">{{ $m->name }}</option>
                  </select>
                </div>
                <input type="hidden"
                       name="metros_color[]"
                       value="{{ $m->color }}">
                <input type="number"
                       min="1"
                       name="metros_time[]"
                       value="{{ $m->distance }}"
                       class="field field_small station-field form-control"
                       required>
                <p class="text">минут пешком до объекта</p>
                <button onclick="deleteMetro({{ $m->id }})"
                        type="button"
                        class="mx-3 button button_blue w-auto px-3"
                >
                  -
                </button>
              </div>
            </div>
          @empty
            <div class="col-12" data-id="1">
              <div class="d-flex align-items-center station">
                <div class="select" style="width: 45%">
                  <select name="metros[]"
                          class="form-control field metros w-100"
                          required>
                  </select>
                </div>
                <input type="hidden" name="metros_color[]">
                <input type="number" min="1" name="metros_time[]"
                       class="field field_small station-field" required>
                <p class="text">минут пешком до объекта</p>
                <button onclick="deleteMetro(1)"
                        type="button"
                        class="mx-3 button button_blue w-auto px-3">
                  -
                </button>
              </div>
            </div>
          @endforelse

        </div>
        <div class="row part__bottom">
          <div class="col-12">
            <button onclick="addMetro()"
                    {!! $hotel->metros()->count() >= 3 ? 'style="display: none"' : '' !!}
                    type="button" class="button button_blue"
            >
              Добавить станцию
            </button>

          </div>
        </div>
        @error('metros')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
        @enderror
        <div class="row part__bottom">
          <div class="col-12">
            <button class="button button_blue"
                    type="submit">
              Сохранить
            </button>
          </div>
        </div>
      </form>
    </div>
  </section>

  <section class="part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_blue">Прайс лист отеля</h2>
        </div>
      </div>
      <div class="row part__middle">
        <div class="col-12">
          <p class="caption">Стоимость и условия проживания в данном разделе, формируются автоматически, как только Вы
            установите цены в Разделе "Календарь цен",система автоматически выберет самое доступное предложение и
            укажет его как минимальную цену в Объекте.</p>
        </div>
      </div>

      <div class="row part__content">
        <div class="col-12">
          <table class="prices">
            <tbody>
            @foreach ($hotel->minimals as $min)
              <tr>
                @if ($min->value !== 0)
                  <td class="prices__main">{{ $min->name }} - от {{ $min->value }} руб.</td>
                  <td class="text">{{ $min->info }}</td>
                @else
                  <td class="prices__main">{{ $min->name }}</td>
                  <td class="text">{{ $min->info }}</td>
                @endif
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <section class="part gray">
    <form action="{{ route('moderator.object.update', $hotel->id) }}" method="post">
      @csrf
      <input type="hidden" name="type_update" value="attr">
      <div class="container">
        <div class="row part__top">
          <div class="col-12">
            <h2 class="title title_blue">Детально об отеле</h2>
          </div>
        </div>
        <div class="row part__middle">
          <div class="col-12">
            <p class="caption">Выберите пункты наиболее точно отражающие преимущества Вашего объекта размещения (минимум
              3, максимум 9 пунктов).</p>
          </div>
        </div>

        <div class="row part__content">
          <div class="col-12">
            <ul class="details">
              @foreach($attributeCategories as $category)
                <li class="detail">
                  <p class="text-bold_small details__title">{{ $category->name }}</p>

                  @foreach($category->attributes as $attr)
                    <div class="choice">
                      <input type="hidden" id="attr-{{ $attr->id }}-h" name="attr[{{ $attr->id }}]" value="false">
                      <input type="checkbox"
                             id="attr-{{ $attr->id }}"
                             value="true"
                             name="attr[{{ $attr->id }}]"
                          {{ $hotel->attrs->contains('id', $attr->id) ? 'checked' : '' }}
                      >
                      <div class="check">
                        <div class="check__flag check__flag_blue"></div>
                      </div>
                      <label for="attr-{{ $attr->id }}">{{ $attr->name }}</label>
                    </div>
                  @endforeach
                </li>
              @endforeach
            </ul>
          </div>

        </div>
        <div class="row part__bottom">
          <div class="col-12">
            <button class="button button_blue"
                    type="submit">
              Обновить
            </button>
          </div>
        </div>
      </div>
    </form>
  </section>

  <section class="part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_blue">Фото объекта</h2>
        </div>
      </div>
      <div class="row part__middle">
        <div class="col-12">
          <p class="caption">Загрузите фотографии объекта размещения. По этим фотографиям клиент сможет составить общее представление о Вашем объекте и выбрать его. Рекомендуем загрузить самые лучшие фотографии объекта. (минимум 1 фотография, максимум 6)</p>
        </div>
      </div>

      <div class="row part__content">
        <div class="col-12">
          <ul class="uploud all-slides">

            @foreach($hotel->images as $image)
              <li class="uploud__item"
                  data-image-id="{{ $image->id }}"
                  data-image-delete="{{ route('moderator.image.delete', '') }}"
                  data-image-moderate="{{ route('moderator.image.moderate', '') }}"
              >
                <div class="uploud__thumb uploud__thumb_admin"
                     style="background-image: url('{{ url($image->path) }}'); background-size: cover;"
                     id="upload{{$image->id}}">
                  <span class="upload_number">№ {{ $loop->index + 1 }}</span>
                  @if($image->moderate)
                    <div class="moderate">
                      <img src="{{ asset('img/lk/arrow-top.png') }}" alt="">
                    </div>
                  @endif
                  <div class="remove-photo">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                  </div>
                </div>
                <p class="uploud__status {{ !$image->moderate ? 'uploud__status_good' : '' }}">
                  {{ $image->moderate ? 'Проверка модератором' : 'Опубликовано' }}
                </p>
              </li>
            @endforeach

          </ul>
        </div>
      </div>

      <div class="row part__content">
        @if($hotel->moderate)
          <div class="col-auto">
            <form action="{{ route('moderator.object.upload', $hotel->id) }}" method="post">
              @csrf
              <button type="submit" class="button button_blue" {{ $hotel->rooms()->count() <= 0 ? 'disabled' : '' }}>Опубликовать <i class="fa fa-upload d-block ml-3"></i></button>
            </form>
          </div>
        @endif
      </div>

    </div>
  </section>



  <div class="overlay"></div>

  {{-- Update Phone --}}
  <div class="popup popup_horizontal" id="popupObject">
    <img src="{{ asset('img/lk/close.png') }}" alt="" class="close-this">
    <h2 class="title title_blue popup__title">Объект</h2>
    <form action="{{ route('moderator.object.update', $hotel->id) }}" method="post">
      @csrf
      <input type="hidden" name="type_update" value="phone">
      <div class="d-flex align-items-center case">
        <input type="text" class="field" name="name" value="{{ $hotel->name }}" placeholder="Название объекта">
        <div class="select">
          <input type="hidden" name="type" value="{{ $hotel->type->id }}">
          <div class="select__top">
            <span class="select__current">{{ $hotel->type->name }}</span>
            <img class="select__arrow" src="{{ asset('img/lk/arrow.png') }}" alt="">
          </div>
          <ul class="select__hidden">
            @foreach($hotelTypes as $type)
              <li class="select__item select__item_blue {{ $hotel->type->id === $type->id ? 'active' : '' }}" data-id="{{ $type->id }}">{{ $type->name }}</li>
            @endforeach
          </ul>
        </div>

      </div>

      <div class="d-flex align-items-center case_2">
        <input type="phone" class="field" name="phone" value="{{ $hotel->phone }}" required placeholder="Телефон 1 объекта">
        <input type="phone" class="field" name="phone_2" value="{{ $hotel->phone_2 }}" placeholder="Телефон 2 объекта">
        <input type="email" class="field" name="email" value="{{ $hotel->email }}" required placeholder="E-mail">
      </div>

      <button type="submit" class="button button_blue">Сохранить</button>
    </form>
  </div>

  {{-- Address Update --}}
  <div class="popup popup_horizontal" id="popupAdress">
  <img src="{{ asset('img/lk/close.png') }}" alt="" class="close-this">
  <h2 class="title title_blue popup__title">Адрес</h2>
  <form action="{{ route('moderator.object.update', $hotel->id) }}" method="post">
    @csrf
    <input type="hidden" value="address" name="type_update">
    <div class="d-flex align-items-center">
      <p class="text-bold adress">Адрес объекта: </p>
      <div class="position-relative w-100">
        <input type="text" id="address" name="value" class="field w-100" value="{{ $hotel->address->value }}" required placeholder="Введите адрес">
      </div>
    </div>
    <div class="d-flex align-items-start">
      <p class="text-bold adress">Комментарий к адресу: </p>
      <input class="field" name="comment" value="{{ $hotel->address->comment }}" placeholder="Введите текст">
    </div>
    <button type="submit" class="button button_blue popup__bottom">Сохранить</button>
  </form>
</div>

@endsection

@section('js')
  <script>
    $(document).ready(function () {
      selectInit()

      $('.uploud').sortable({
        items: '.uploud__item',
        update: updateOrderPhotos
      });
    })

    $("#address").suggestions({
      token: "a35c9ab8625a02df0c3cab85b0bc2e9c0ea27ba4",
      type: "ADDRESS",
    });

    function selectInit() {
      $('.metros').select2({
        placeholder: "Название станции",
        language: "ru",
        ajax: {
          delay: 250,
          type: "POST",
          headers: {
            "Content-Type": 'application/json',
            "Accept": 'application/json',
            "Authorization": "Token be33fe1fe0328828d9632c248dcad68166e62740"
          },
          url: 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/metro',
          data: function (params) {
            return JSON.stringify({
              query: params.term,
              filters: [
                {
                  "city": '{{ $hotel->address->city }}',
                }
              ]
            })
          },
          processResults: function (data) {
            return {
              results: data.suggestions.map((e) => {
                return {
                  text: e.value,
                  id: e.value,
                  color: e.data.color
                };
              })
            };
          }
        }
      });
    }

    $('.metros').on("select2:select", takeColor);

    function takeColor(e) {
      console.log($(e.currentTarget).parent().parent().children('input[type="hidden"]').get(0).value = e.params.data.color)
      console.log(e.params.data.color);
    }

    function deleteMetro(id) {
      count_metros--;
      let str = '[data-id=' + id + ']'
      console.log($(str).remove())
    }

    function addMetro() {
      if (count_metros < 3) {
        metros_ids++;
        count_metros++;
        $('#metro').append(
          "<div class='col-12' data-id='" + metros_ids + "'>" +
          "<div class='d-flex align-items-center station'>" +
          "<div class='select' style='width: 45%'>" +
          "<select name='metros[]' class='form-control field metros w-100' required></select>" +
          "</div>" +
          "<input type='hidden' name='metros_color[]' class='color'>" +
          "<input type='number' name='metros_time[]' class='form-control field field_small station-field' required>" +
          "<p class='text'>минут пешком до объекта</p>" +
          "<button onclick='deleteMetro(" + metros_ids + ")' class='mx-3 button button_blue w-auto px-3'>-</button>" +
          "</div>" +
          "</div>"
        )

        selectInit()

        $('.metros').on("select2:select", takeColor);
      }
    }

    let metros_ids = {{ $hotel->metros->pluck('distance')->max() ?? 1 }};

    let count_metros = {{ $hotel->metros()->count() > 0 ? $hotel->metros()->count() : 1 }};
    $("input[type='phone']").mask("+7 (999) 999 99-99");
    $("input[name*='attr'][type='checkbox']").on( "change", function() {
      console.log(2);
      if (+$("input[name*='attr'][type='checkbox']:checked").length > 9)
      {
        this.checked=false;
      } else if ($("input[name*='attr'][type='checkbox']:checked").length < 3) {
        this.checked = true;
      }
    });
  </script>
@endsection