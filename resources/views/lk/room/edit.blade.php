@extends('lk.layouts.app')

@section('content')
  <input type="hidden" name="category.update" value="{{ route('lk.category.update') }}">
  <input type="hidden" name="category.create" value="{{ route('lk.category.create') }}">
  <input type="hidden" name="category.delete" value="{{ route('lk.category.delete', '') }}">

  <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
  <section class="part">
    <div class="container">
      <div class="row demonstration">
        <div class="col-12">
          <p class="text">Демонстрация каждого номера объекта в отдельности</p>
        </div>

      </div>
      <div class="row">
        <div class="col-12">
          <div class="d-flex align-items-center category">
            <h2 class="title">Категории номеров</h2>
            <button class="category__add">
              <span>Добавить категорию</span>
              <span class="plus">+</span>
            </button>
          </div>
          <ul class="categories">

            @foreach($hotel->categories as $category)
              <li class="categories__item" data-id="{{ $category->id }}">
                <div class="categories__first categories__first_big">
                  <span class="categories__name categories__hide">{{ $category->name }}</span>
                  <input type="text" class="field field_hidden field_hidden-room" placeholder="Введите категорию">
                </div>

                <div class="categories__control">
                  <button class="categories__custom change-category" id="">
                    <img class="category-change" src="{{ asset('img/lk/pen.png') }}" alt="">
                    <img class="category-good" src="{{ asset('img/lk/check.png') }}" alt="">
                  </button>
                  <button class="categories__custom categories__custom_2 categoryRemove" id="">
                    <img class="category-bin" src="{{ asset('img/lk/bin.png') }}" alt="">
                  </button>
                </div>
              </li>
            @endforeach

            <li class="categories__item">
              <div class="categories__first categories__first_big">
                <span class="categories__name categories__hide"></span>
                <input type="text" class="field field_hidden field_hidden-room" placeholder="Введите категорию">
              </div>

              <div class="categories__control">
                <button class="categories__custom change-category" id="">
                  <img class="category-change" src="{{ asset('img/lk/pen.png') }}" alt="">
                  <img class="category-good" src="{{ asset('img/lk/check.png') }}" alt="">
                </button>
                <button class="categories__custom categories__custom_2 categoryRemove" id="">
                  <img class="category-bin" src="{{ asset('img/lk/bin.png') }}" alt="">
                </button>
              </div>
            </li>

          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="part category-list">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <div class="d-flex align-items-center rooms-head">
            <h2 class="title">Список номеров</h2>
            <button class="room__add">
              <span>Добавить номер</span>
              <span class="plus">+</span>
            </button>
          </div>
        </div>
      </div>

      {{--      Rooms   --}}
      @foreach($rooms as $room)
        <div class="shadow shadow-complete">
          <div class="row row__head {{ $room->moderate ? '' : 'row__head_blue' }}">
            <div class="col-1">
              <p class="head-text">#{{ $room->order }}</p>
            </div>
            <div class="col-1 offset-sm-1">
              <p class="head-text">№ {{ $room->number }}</p>
            </div>
            <div class="col-2 offset-sm-1">
              <p class="head-text head-text_bold">{{ $room->name }}</p>
            </div>
            <div class="col-3 offset-sm-2">
              <p class="head-text">{{ $room->category ? $room->category->name : '' }}</p>
            </div>
            <div class="col-1 text-right">
              <button class="quote__remove text-white">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </div>

{{--          Status--}}
          @if($room->moderate)
            <div class="row">
              <div class="col-12">
                <p class="text quote__status quote__status_red">Проверка модератором</p>
              </div>
            </div>
          @else
            <div class="row">
              <div class="col-12">
                <p class="text quote__status quote__status_blue">Опубликовано</p>
              </div>
            </div>

          @endif


          <div class="row room-details">
            <div class="col-2">

              <label class="room-text" for="orderRoom-{{ $room->id }}">Ордер</label>
              <input type="text"
                     name="order"
                     class="field field_border"
                     id="orderRoom-{{ $room->id }}"
                     placeholder="#1"
                     value="{{ $room->order }}">


            </div>
            <div class="col-2">

              <label class="room-text" for="numberRoo-{{ $room->id }}m">Номер</label>
              <input type="text"
                     name="number"
                     class="field field_border"
                     id="numberRoom-{{ $room->id }}"
                     placeholder="№1"
                     value="{{ $room->number }}">


            </div>
            <div class="col-4">

              <label class="room-text" for="nameRoom-{{ $room->id }}">Название</label>
              <input type="text"
                     name="name"
                     class="field field_border"
                     id="nameRoom-{{ $room->id }}"
                     placeholder="Название"
                     value="{{ $room->name }}">


            </div>
            <div class="col-4">
              <p class="room-text">
                Категория
              </p>
              <div class="select" id="selectRoom">
                <input type="hidden" name="category_id" value="{{ $room->category->id ?? '' }}">
                <div class="select__top select__top_100">
                  <span class="select__current">{{ $room->category->name ?? 'Категория' }}</span>
                  <img class="select__arrow" src="{{ asset('img/lk/arrow.png') }}" alt="">
                </div>
                <ul class="select__hidden">
                  @foreach($hotel->categories as $category)
                    <li class="select__item {{ $room->category ? $room->category->id === $category->id ? 'active' : '' : '' }}" data-id="{{ $category->id }}">{{ $category->name }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="uploud-photo" id="file-dropzone"></div>
              <ul class="visualizacao sortable dropzone-previews" id="original_items">
              </ul>
              <ul id="cloned_items">
              </ul>
              <div class="preview" style="display:none;">
                <li>
                  <div>
                    <div class="dz-preview dz-file-preview">
                      <img data-dz-thumbnail/>
                      <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                      <div class="dz-success-mark"><span>Проверка модератором</span></div>
                      <div class="dz-error-mark"><span>✘</span></div>
                      <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                  </div>
                </li>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-12">
              <p class="uploud__min text">
                (минимум 1 фотография, максимум 6)
              </p>
            </div>
          </div>
          <div class="row">
            <ul class="hours">
              @foreach($costTypes as $type)
                @php
                  $costRoom = $room->costs()->where('period_id', $type->id)->first();
                @endphp
                <li class="hour">
                  <p class="heading hours__heading">
                    {{ $type->name }}
                  </p>
                  <div class="d-flex align-items-center">
                    <input type="text"
                           class="field hours__field"
                           placeholder="{{ $costRoom->value ?? '0000' }}"
                           value="{{ $costRoom->value ?? '' }}">

                    <div class="hours__hidden">
                      <span class="hours__money">{{ $costRoom->value ?? '0000' }}</span>
                      <span class="hours__rub">руб.</span>
                    </div>

                    <span class="rub">руб.</span>
                    <div class="select hours__select">
                      <div class="select__top">
                        <span class="select__current">От 2-х часов</span>
                        <img class="select__arrow" src="{{ asset('img/lk/arrow.png') }}" alt="">
                      </div>
                      <ul class="select__hidden">
                        @foreach($type->periods as $period)
                          <li class="select__item">{{ $period->info }}</li>
                        @endforeach
                      </ul>
                    </div>
                    <span class="hours__after">
                      От 2-х часов
                    </span>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
          <div class="row more-details">
            <div class="col-12">
              <p class="text">Детально о номере</p>
              <p class="caption caption_mt">
                Выберите пункты наиболее точно отражающие преимущества данного номера
                / группы номеров. (минимум 3, максимум 9 пунктов)
              </p>
            </div>

          </div>

          <div class="row">
            <div class="col-12">
              <a class="show-all show-all_orange">Показать все</a>
            </div>
          </div>
          <div class="row row__bottom">
            <div class="col-12">
              <div class="d-flex align-items-center quote__buttons">
                <button class="button save-room" id="saveRoom">Сохранить</button>
                <button class="quote__read quote__read_1">
                  <img src="{{ asset('img/lk/pen.png') }}" alt="">
                </button>
                <button class="quote__remove remove-btn">
                  <i class="fa fa-trash"></i>
                </button>

              </div>
            </div>
          </div>

        </div>
      @endforeach
    </div>
  </section>


@endsection

@section('js')
  <script>

    $(document).ready(function () {
      $('.sortable').sortable({
        items: '.dz-image-preview',
      });

    });

    Dropzone.autoDiscover = false;
    // instantiate the uploader
    if ($('#file-dropzone').hasClass('dropzone_disabled')) {

    } else {
      $('#file-dropzone').dropzone({

        url: "/file/post",
        maxFiles: 6,
        thumbnailWidth: 352,
        thumbnailHeight: 260,
        addRemoveLinks: true,
        previewsContainer: '.visualizacao',
        previewTemplate: $('.preview').html(),
        init: function () {


          this.on("complete", function (file) {
            $(".dz-remove").html("<span class='upload__remove'><i class='fa fa-trash' aria-hidden='true'></i></span>");
            $('#file-dropzone').appendTo('.visualizacao')
          });

          this.on('completemultiple', function (file, json) {

            // $('.sortable').sortable({
            // 	items: '.dz-image-preview',
            // });

            if (this.files.length > 6) {
              this.removeFile(this.files[0]);
            }

          });

          // $('.uploud-photo').draggable( "disable" )
          this.on('success', function (file, json) {

          });

          this.on('addedfile', function (file) {

          });


          this.on("reset", function (file) {
            $('#file-dropzone').show()

          });

          this.on('queuecomplete', function (file) {
            $(this).parents(".shadow").find('.uploud__min').hide()
          });
        }
      });
    }

  </script>
@endsection