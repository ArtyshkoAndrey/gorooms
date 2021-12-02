<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <base href="{{ URL::to('/'); }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE">
  <meta name="format-detection" content="telephone=no">
  <meta name="format-detection" content="address=no">
  <meta http-equiv="x-rim-auto-match" content="none">

  <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('favicon.svg') }}" type="image/x-icon">
  <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
  <link rel="canonical" href="{{ url(Request::url()) }}"/>
  <link rel="stylesheet" href="{{ asset('css/metro.css') }}">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if(\Route::getCurrentRoute()->getName() === 'hotels.show' && isset($hotel))
    <title>{{ $hotel->meta_title }}</title>
    <meta name="keywords" content="{{ $hotel->meta_keywords ?? '' }}">
    <meta name="author" content="{{ $hotel->name ?? ''}}">
    <meta name="description" content="{{ $pageAbout->meta_description ?? ''}}">
    <meta property="og:locale" content="ru_RU"/>
    <meta property="og:type" content="article"/>
    <meta property="og:title" content="{{ $pageAbout->title ?? '' }}"/>
    <meta property="og:description" content="{{ $pageAbout->meta_description ?? ''}}"/>
    <meta property="og:url" content="https://gorooms.ru{{ $pageAbout->url ?? '' }}"/>
    <meta property="og:image" content="https://gorooms.ru/img/logo.svg"/>
    <meta property="og:site_name" content="https://gorooms.ru/"/>    @if($hotel->meta_keywords)
      <link rel="canonical" href="{{ $hotel->meta_keywords }}"/>
    @endif
  @else
    @if( isset($pageDescription) && !is_null($pageDescription) ?? get_class($pageDescription) == 'App\Models\PageDescription')
      <title>{{ $pageDescription->title ?? config('app.name', 'GoRooms') }}</title>
      <meta name="description" content="{{ $pageDescription->meta_description ?? ''}}">
      <meta name="keywords" content="{{ $pageDescription->meta_keywords ?? '' }}">
        <link rel="canonical" href="{{ $pageDescription->meta_keywords ?? '' }}"/>
    @elseif( isset($pageAbout))
      <title>{{ $pageAbout->title ?? config('app.name', 'GoRooms') }}</title>
      <meta name="description" content="{{ $pageAbout->meta_description ?? '' }}">
      @if( $pageAbout->meta_keywords)
        <link rel="canonical" href="{{ $pageAbout->meta_keywords }}"/>
      @endif
    @else
      <title>{{ config('app.name', 'GoRooms') }}</title>
    @endif
  @endif

<!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,700,900&display=swap&subset=cyrillic"
        rel="stylesheet">

  <!-- Styles -->

  <link rel="stylesheet" href="{{ asset('css/bootstrap-grid-validation-modal-tabs-print.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jquery.formstyler.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/swiper.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jquery.flexdatalist.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=2">
  <style>
      .ml-0 {
          margin-left: 0 !important;
      }
      .mb-3 {
          margin-bottom: 0.75rem !important;
      }
  </style>
  <link rel="canonical" href="{{ url(Request::url()) }}"/>
  <script src="https://api-maps.yandex.ru/2.1/?apikey={{ config('services.yandex.map.key') }}&lang=ru_RU"
          type="text/javascript">
  </script>
  @stack('header')

  @if(config('app.debug'))
    <script src="{{ asset('js/vue-dev.js') }}"></script>
  @else
    <script src="{{ asset('js/vue@2.js') }}"></script>
  @endif
</head>

<body>
<header class="header">
  <div class="header-top">
    <div class="container header-top-container">
      @if(is_null(\Route::getCurrentRoute()->getName()))
        <div class="header-logo">
          <img src="{{ asset('img/logo.svg') }}" alt="" class="header-logo-img">
        </div>
      @else
        <a href="/" class="header-logo">
          <img src="{{ asset('img/logo.svg') }}" alt="" class="header-logo-img">
        </a>
      @endif
      <div class="header-top-btns">
        @auth
          @if(!auth()->user()->hotel()->exists())
            @if (!auth()->user()->is_moderate)
              <a href="{{ route('lk.start') }}"
                 class="header-top-btn header-top-btn-object btn btn-sm btn-light-border">
                Зарегистрировать свой объект
              </a>
            @endif
          @else
            <a href="{{ route('lk.index') }}" class="header-top-btn header-top-btn-object btn btn-sm btn-light-border">Личный кабинет</a>
          @endif
        
          @if(auth()->user()->is_admin)
            <a href="{{ route('admin.index') }}" class="header-top-btn btn btn-sm btn-light"
               style="width: auto; padding: 0 10px">Административная панель</a>
          @endif
          <a href="{{ route('logout') }}"
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
             class="header-top-btn btn btn-sm btn-light"
             style="width: auto; padding: 0 10px">Выйти</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST"
                style="display: none;">
            @csrf
          </form>
        @else
          <a href="{{ route('lk.start') }}"
             class="header-top-btn header-top-btn-object btn btn-sm btn-light-border">
            Зарегистрировать свой объект
          </a>
          <a href="{{ route('login') }}" class="header-top-btn header-top-btn-signin btn btn-sm btn-light">Войти</a>
{{--          <a href="{{ route('register') }}" class="header-top-btn header-top-btn-reg btn btn-sm btn-light">Регистрация</a>--}}
        @endauth
      </div>
      <button id="js-menu-btn" class="header-menu-btn" type="button">
        <span class="header-menu-btn-item header-menu-btn-item-1"></span>
        <span class="header-menu-btn-item header-menu-btn-item-2"></span>
        <span class="header-menu-btn-item header-menu-btn-item-3"></span>
      </button>
    </div>
  </div>
  <nav id="js-menu-wrapper" class="header-menu-wrapper">
    <div class="container header-menu-container">
      <ul id="js-menu" class="header-menu">
        <li class="header-menu-item">
          <a href="{{ route('hotels.index') }}" class="header-menu-link">Биржа отелей</a>
        </li>
        <li class="header-menu-item">
          <a href="{{ route('rooms.index') }}" class="header-menu-link">Биржа номеров</a>
        </li>
        <li class="header-menu-item">
          <a href="{{ route('search.map') }}" class="header-menu-link">Поиск по карте <img
                src="{{ asset('img/ico-search.svg') }}" alt=""></a>
        </li>
        <li class="header-menu-item">
          <a href="/bonuse" class="header-menu-link">Бонусная программа</a>
        </li>
        <li class="header-menu-item">
          <a href="/contacts" class="header-menu-link">Контакты</a>
        </li>

        @auth
          @if(!auth()->user()->hotel()->exists() && !auth()->user()->is_moderate)
            @if (!auth()->user()->is_moderate)
              <li class="header-menu-item header-menu-item-mobile">
                <a href="{{ route('lk.start') }}"
                   class="header-top-btn header-top-btn-object btn btn-sm btn-light-border">
                  Зарегистрировать свой объект
                </a>
              </li>
            @endif
          @else
            <li class="header-menu-item header-menu-item-mobile">
              <a href="{{ route('lk.index') }}" class="header-top-btn header-top-btn-object btn btn-sm btn-light-border">Личный кабинет</a>
            </li>
          @endif

          @if(auth()->user()->is_admin)
            <li class="header-menu-item header-menu-item-mobile">
              <a href="{{ route('admin.index') }}" class="header-top-btn btn btn-sm btn-light"
                 style="width: auto; padding: 0 10px">Административная панель</a>
            </li>
          @endif
          <li class="header-menu-item header-menu-item-mobile">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="header-top-btn btn btn-sm btn-light"
               style="width: auto; padding: 0 10px">Выйти</a>
          </li>
        @else
          <li class="header-menu-item header-menu-item-mobile">
            <a href="{{ route('lk.start') }}"
               class="header-top-btn header-top-btn-object btn btn-sm btn-light-border">
              Зарегистрировать свой объект
            </a>
          </li>
          <li class="header-menu-item header-menu-item-mobile">
            <a href="{{ route('login') }}" class="header-top-btn header-top-btn-signin btn btn-sm btn-light">Войти</a>
          </li>
          <li class="header-menu-item header-menu-item-mobile">
{{--            <a href="{{ route('register') }}" class="header-top-btn header-top-btn-reg btn btn-sm btn-light">Регистрация</a>--}}
          </li>
        @endauth
      </ul>
    </div>
  </nav>
</header>
<div class="page-wrapper @stack('wrapper-class')">
  @yield('content')
</div>
<footer class="footer">
  <div class="container footer-container">
    <ul class="footer-menu">
      <li class="footer-menu-item">
        <a href="{{ route('hotels.index') }}" class="footer-menu-link">Отели</a>
      </li>
      <li class="footer-menu-item">
        <a href="{{ route('rooms.index') }}" class="footer-menu-link">Номера</a>
      </li>
      <li class="footer-menu-item">
        <a href="{{ route('search.map') }}" class="footer-menu-link">Поиск по карте</a>
      </li>
      <li class="footer-menu-item">
        <a href="/rules" class="footer-menu-link">Правила бронирования</a>
      </li>
      <li class="footer-menu-item">
        <a href="/bonuse" class="footer-menu-link">Бонусная программа</a>
      </li>
      <li class="footer-menu-item">
        <a href="/contacts" class="footer-menu-link">Контакты</a>
      </li>
    </ul>
    <div class="footer-bottom">
      <div class="footer-contacts">
        <a href="tel:{{ Settings::option('phone') }}" class="footer-contacts-item footer-contacts-item-tel">
          <span>{{ Settings::option('phone') }}</span>
        </a>
        <a href="mailto:{{ Settings::option('email') }}"
           class="footer-contacts-item footer-contacts-item-email">
          <span>{{ Settings::option('email') }}</span>
        </a>
        <p class="footer-contacts-item footer-contacts-item-address">{{ Settings::option('address') }}</p>
      </div>
      <div class="footer-soc">
        @if($link = Settings::option('fb'))
          <a href="{{ $link }}" rel="nofollow" target="_blank" class="footer-soc-link">
            <img src="{{ asset('img/ico-facebook.svg') }}" alt="" width="40" height="40">
          </a>
        @endif
        @if($link = Settings::option('instagram'))
          <a href="{{ $link }}" rel="nofollow" target="_blank" class="footer-soc-link">
            <img src="{{ asset('img/ico-insta.svg') }}" alt="" width="40" height="40">
          </a>
        @endif
        @if($link = Settings::option('vk'))
          <a href="{{ $link }}" rel="nofollow" target="_blank" class="footer-soc-link">
            <img src="{{ asset('img/ico-vk.svg') }}" alt="" width="40" height="40">
          </a>
        @endif
        @if($link = Settings::option('youtube'))
          <a href="{{ $link }}" rel="nofollow" target="_blank" class="footer-soc-link">
            <img src="{{ asset('img/ico-youtube.svg') }}" alt="" width="40" height="40">
          </a>
        @endif
      </div>
    </div>
  </div>
</footer>

<div id="signin-popup" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"></button>
      <p class="modal-text-inprogress">Раздел находится в разработке</p>
    </div>
  </div>
</div>
<div id="reg-popup" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"></button>
      <p class="modal-text-inprogress">Раздел находится в разработке</p>
    </div>
  </div>
</div>
<div id="obj-popup" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"></button>
      <p class="modal-text-inprogress">Раздел находится в разработке</p>
    </div>
  </div>
</div>
<div id="book-popup" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog book-modal-dialog" role="document">
    <div class="modal-content" id="vue-booking">

      <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"></button>
      <div class="h3 modal-title">
        Вы выбрали номер “<span>@{{ number_name }}</span>”
        <span>@{{ category_name }}</span>
        в объекте размещения “<span>@{{ roomInfo.hotel.name }}</span>”
      </div>
      <form id="book-form" class="book-form" action="" method="post" :action="action" @submit="submit">
        @csrf
        <div class="row">
          <div class="col-lg-6 row mb-3 align-content-start">
            <div class="col-12">
              <div class="row mb-3">
                <div class="col-12 col-lg-4" v-if="costs.byHour.exists">
                  <input type="radio" id="order_at_" class="checkbox" checked
                         name="order_at" value="hour" @change="setForm('hour')">
                  <label for="order_at_"
                         class="search-filter-label checkbox-label checkbox-label-light">На
                    час</label>
                </div>
                <div class="col-12 col-lg-4"  v-if="costs.byNight.exists">
                  <input type="radio" id="order_at_night" class="checkbox"
                         name="order_at" value="night" @change="setForm('night')">
                  <label for="order_at_night"
                         class="search-filter-label checkbox-label checkbox-label-light">На
                    ночь</label>
                </div>
                <div class="col-12 col-lg-4"  v-if="costs.byDay.exists">
                  <input type="radio" id="order_at_day" class="checkbox"
                         name="order_at" value="day"  @change="setForm('day')">
                  <label for="order_at_day"
                         class="search-filter-label checkbox-label checkbox-label-light">На
                    сутки</label>
                </div>
              </div>
            </div>
            <div class="col-12 mb-3">
              <div class="form-group-dates">
                <div class="form-group-date">
                  <p class="form-group-date-label">Заезд:<span>*</span></p>
                  <input id="from-date" name="from-date" type="date"
                         :disabled="dateTime.fromDate.disabled"
                         v-model="dateTime.fromDate.value"
                         class="form-control form-control-date" placeholder="06.06.2021" required>
                  <input name="from-time" type="time"
                         :disabled="dateTime.fromTime.disabled"
                         v-model="dateTime.fromTime.value"
                         class="form-control form-control-time" placeholder="14:00" required>
                </div>
              </div>
            </div>
            <div class="col-12 mb-3">
              <div class="form-group-dates">
                <div class="form-group-date">
                  <p class="form-group-date-label">Выезд:<span>*</span></p>
                  <input id="to-date" name="to-date" type="date"
                         :disabled="dateTime.toDate.disabled"
                         v-model="dateTime.toDate.value"
                         class="form-control form-control-date" placeholder="06.06.2021" required>
                  <input id="to-time" name="to-time" type="text"
                         :disabled="dateTime.toTime.disabled"
                         v-model="dateTime.toTime.value"
                         class="form-control form-control-time" placeholder="14:00" required>
                </div>
              </div>
            </div>
            <div class="col-12 mb-3" id="book-on-title"  v-if="showBookOn">
              <div>
                <p class="form-group-date-label">Забронировать на:</p>
              </div>
            </div>

            <div class="col-12" v-if="showBookOn">
              <div class="row ml-0">
                <div class="col-12 col-lg-3" v-if="1 >= costs.byHour.start_at">
                  <input type="radio" id="book_by_1_hour" class="checkbox"
                         name="book_by" value="1" @change="currentHours = 1">
                  <label for="book_by_1_hour"
                         class="search-filter-label checkbox-label checkbox-label-light">1 час</label>
                </div>
                <div class="col-12 col-lg-3"  v-if="2 >= costs.byHour.start_at">
                  <input type="radio" id="book_by_2_hour" class="checkbox"
                         name="book_by" value="2" @change="currentHours = 2">
                  <label for="book_by_2_hour"
                         class="search-filter-label checkbox-label checkbox-label-light">2
                    часа</label>
                </div>
                <div class="col-12 col-lg-3"  v-if="3 >= costs.byHour.start_at">
                  <input type="radio" id="book_by_3_hour" class="checkbox"
                         name="book_by" value="3" @change="currentHours = 3">
                  <label for="book_by_3_hour"
                         class="search-filter-label checkbox-label checkbox-label-light">3
                    часа</label>
                </div>
                <div class="col-12 col-lg-3">
                  <input type="radio" id="book_by_4_hour" checked class="checkbox"
                         name="book_by" value="4" @change="currentHours = 4">
                  <label for="book_by_4_hour"
                         class="search-filter-label checkbox-label checkbox-label-light">4
                    часа</label>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <input id="book-name" name="book-name" type="text" class="form-control"
                         placeholder="Имя*"
                         required>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <input id="book-tel" name="book-tel" type="tel" class="form-control"
                         placeholder="Тел:* +7 (___) ___ __ __" required>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                            <textarea name="book-comment" id="book-comment" class="form-control"
                                      placeholder="Комментарий"></textarea>
                </div>

                <p class="form-disclaimer">Нажимая &laquo;Забронировать&raquo; Вы даете согласие на&nbsp;обработку
                  персональных данных и&nbsp;соглашаетесь c&nbsp;<a href="/privacy-policy">пользовательским
                    соглашением и&nbsp;политикой
                    конфиденциальности</a>.</p>
                <button class="btn btn-orange btn-big btn-form" type="submit">Забронировать</button>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="form-group">

            </div>
          </div>
        </div>
      </form>

    </div>
    <script>
      const Booking = new Vue({
        el: '#vue-booking',
        data() {
          return {
            roomInfo2: '',
            roomInfo: {
              id: '',
              category_id: '',
              name: '',
              category: {
                name: ''
              },
              hotel: {
                name: ''
              }
            },
            action : '',
            costs: {
              byHour: {
                exists: false,
                start_at: '0',
              },
              byNight: {
                exists: false,
                start_at: '',
                end_at: ''
              },
              byDay: {
                exists: false,
                start_at: '',
                end_at: ''
              },
            },
            dateTime: {
              fromDate: {
                value: '',
                disabled: false,
              },
              fromTime: {
                value: '',
                disabled: false,
              },
              toDate: {
                value: '',
                disabled: true,
              },
              toTime: {
                value: '',
                disabled: true,
              },
            },
            showBookOn: true,
            currentHours: 4,
            currentPosition: 'byHours'
          }
        },
        watch: {
          currentHours: function () {
            this.setHoursTime();
          },
          'dateTime.fromDate.value': function (value) {
            if (this.showBookOn) {
              this.setHoursTime();
            }
            if (this.currentPosition === 'byNight') {
              let from = new Date(this.dateTime.fromDate.value);
              from.setHours(this.costs.byNight.start_at.substr(0,1));
              from.setMinutes(this.costs.byNight.start_at.substr(3,5))
              let to = new Date(from.getTime());
              to.setHours(this.costs.byNight.end_at.substr(0,1));
              to.setMinutes(this.costs.byNight.end_at.substr(3,5));
              to.setDate(to.getDate()+1)
              this.setDate(from, to)
            }
          },
          'dateTime.fromTime.value': function (value) {
            if (this.showBookOn) {
              let from = new Date();
              from.setHours(value.slice(0,2));
              let min = value.slice(3,5);
              from.setMinutes(min)
              let to = new Date(from.getTime());
              to.setHours(to.getHours() + 1 + this.currentHours);
              this.setDate(from, to)
            }
          }
        },
        mounted() {
          let dateNow = new Date();
          let dateEnd = new Date();
          dateNow.setHours(dateNow.getHours()+1);
          dateEnd.setHours(dateNow.getHours()+2);
          this.setDate(dateNow, dateEnd);
          this.currentHours = 4;
        },
        computed: {
          category_name: function () {
            return this.roomInfo.category_id ? 'в категории "' + this.roomInfo.category.name + '"' : '';
          },
          number_name: function () {
            return this.roomInfo.name ?? this.roomInfo.id;
          }
        },
        methods: {
          submit() {
            this.dateTime.fromDate.disabled = false;
            this.dateTime.fromTime.disabled = false;
            this.dateTime.toDate.disabled = false;
            this.dateTime.toTime.disabled = false;
          },
          setHoursTime() {
            let from = new Date()
            from.setHours(from.getHours()+1);
            let to = new Date();
            to.setHours(to.getHours() + 1 + this.currentHours);
            this.setDate(from, to, true)
          },
          showFormBookRoom(room_id) {
            this.action = location.protocol + '//' + location.host + "/rooms/" + room_id;

            fetch("/api/room-info/" + room_id)
                    .then(response => response.json())
                    .then(data => (this.roomInfo = data.room_info))
                    .then(data => (this.setCosts()));
          },
          setCosts(data) {
            let byHour = this.roomInfo.costs.find(el => el.period.type.id === 1);
            let byNight = this.roomInfo.costs.find(el => el.period.type.id === 2);
            let byDay = this.roomInfo.costs.find(el => el.period.type.id === 3);

            if (byHour === undefined) {
              this.costs.byHour.exists = false;
            } else {
              this.costs.byHour.exists = true;
              this.costs.byHour.start_at = byHour.period.start_at;
            }
            if (byNight === undefined) {
              this.costs.byNight.exists = false;
            } else {
              this.costs.byNight.exists = true;
              this.costs.byNight.start_at = byNight.period.start_at;
              this.costs.byNight.end_at = byNight.period.end_at;
            }
            if (byDay === undefined) {
              this.costs.byDay.exists = false;
            } else {
              this.costs.byDay.exists = true;
              this.costs.byDay.start_at = byDay.period.start_at;
              this.costs.byDay.end_at = byDay.period.end_at;
            }
          },
          setDate(from, to, ignoreDate = false) {
            from = new Date(from);
            to = new Date(to);
            let dateIn = `${from.getFullYear()}-${('0' + (from.getMonth()+1)).slice(-2)}-${('0' + (from.getDate())).slice(-2)}`;
            let dateOut = `${to.getFullYear()}-${('0' + (to.getMonth()+1)).slice(-2)}-${('0' + (to.getDate())).slice(-2)}`;
            let timeIn = `${('0' + (from.getHours())%24).slice(-2)}:${('0' + from.getMinutes()).slice(-2)}`;
            let timeOut = `${('0' + (to.getHours())%24).slice(-2)}:${('0' + to.getMinutes()).slice(-2)}`;

            if (!ignoreDate) {
              this.dateTime.fromDate.value = dateIn;
              this.dateTime.toDate.value = dateOut;
            }
            this.dateTime.fromTime.value = timeIn;

            this.dateTime.toTime.value = timeOut;
          },
          setForm(value) {
            if (value === 'hour') {
              this.showBookOn = true;
              this.dateTime.fromDate.disabled = false;
              this.dateTime.fromTime.disabled = false;
              this.dateTime.toDate.disabled = true;
              this.dateTime.toTime.disabled = true;

              this.currentHours = 4;
              this.currentPosition = 'byHours';
            }
            else if (value === 'night') {
              this.showBookOn = false;
              this.dateTime.fromDate.disabled = false;
              this.dateTime.fromTime.disabled = true;
              this.dateTime.toDate.disabled = true;
              this.dateTime.toTime.disabled = true;

              this.currentPosition = 'byNight';

              let from = new Date();
              from.setHours(this.costs.byNight.start_at.substr(0,1));
              from.setMinutes(this.costs.byNight.start_at.substr(3,5));
              let to = new Date(from.getTime());
              to.setHours(this.costs.byNight.end_at.substr(0,1));
              to.setMinutes(this.costs.byNight.end_at.substr(3,5));
              to.setDate(to.getDate()+1)
              this.setDate(from, to)
            }
            else if (value === 'day') {
              this.showBookOn = false;
              this.dateTime.fromDate.disabled = false;
              this.dateTime.fromTime.disabled = true;
              this.dateTime.toDate.disabled = false;
              this.dateTime.toTime.disabled = true;

              this.currentPosition = 'byDay';

              let from = new Date();
              from.setHours(this.costs.byDay.start_at.substr(0,1));
              from.setMinutes(this.costs.byDay.start_at.substr(3,4))
              let to = new Date(from.getTime());
              to.setHours(this.costs.byDay.end_at.substr(0,1));
              to.setMinutes(this.costs.byDay.end_at.substr(3,4));
              to.setDate(to.getDate()+1)
              this.setDate(from, to)
            }
          }
        }
      });
      function showFormBookRoom(room_id) {
        Booking.showFormBookRoom(room_id)
      }
    </script>
  </div>
</div>
<div id="message-popup" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"></button>
      <p class="modal-text-inprogress"><img src="{{ asset('img/ico-check-blue.svg') }}" alt="">
        {!!   Session::get('SuccessModalMessage') ?? '' !!}
      </p>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap-modal-tabs-collapse-transition.min.js') }}" defer></script>
<script src="{{ asset('js/jquery.formstyler.min.js') }}" defer></script>
<script src="{{ asset('js/jquery.fancybox.min.js') }}" defer></script>
<script src="{{ asset('js/swiper.min.js') }}" defer></script>
<script src="{{ asset('js/jquery.maskedinput.js') }}" defer></script>
<script src="{{ asset('js/jquery.flexdatalist.min.js') }}" defer></script>
<script src="{{ asset('js/main.js') }}" defer></script>
@yield('scripts')
@if(Session::get('showSuccessModal'))
  @php(Session::forget('showSuccessModal'))
  <script>
    $(function () {
      $('#message-popup').modal('show');
    });
  </script>
@endif
@stack('footer')
<script>
  function extractHostname(url) {
    var hostname;
    //find & remove protocol (http, ftp, etc.) and get hostname
    if (url.indexOf("//") > -1) {
      hostname = url.split('/')[2];
    } else {
      hostname = url.split('/')[0];
    }
    //find & remove port number
    hostname = hostname.split(':')[0];
    //find & remove "?"
    hostname = hostname.split('?')[0];
    return hostname;
  }
  let form = document.getElementById('js-advanced-search');
  if (form) {
    form.addEventListener('change', event => {
      let url = form.action.replace('https', '').replace('http', '').replace('://' + extractHostname(form.action), '')
      fetch('/api' + url, {
        method: 'POST',
        body: new FormData(form)
      })
        .then(response => response.json())
        .then(response => {
          if (response.success) {
            form.querySelectorAll('button[type=submit]')[0].innerText = 'Показать (' + response.payload.count + ') ' + (response.payload.is_room == true ? 'номеров' : 'отелей');
          }
        });
    });
  }
  let options = document.getElementById('advanced-search-location-type');
</script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
  (function (m, e, t, r, i, k, a) {
    m[i] = m[i] || function () {
      (m[i].a = m[i].a || []).push(arguments)
    };
    m[i].l = 1 * new Date();
    k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
  })
  (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
  ym(72285235, "init", {
    clickmap: true,
    trackLinks: true,
    accurateTrackBounce: true,
    webvisor: true
  });
</script>
<noscript>
  <div><img src="https://mc.yandex.ru/watch/72285235" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-73NM6845XT"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());
  gtag('config', 'G-73NM6845XT');
</script>
<script>
  $("input:checkbox.checkbox").on('click', function() {
    if($(this).closest(".advanced-search-details").length == 0) {
      // in the handler, 'this' refers to the box clicked on
      var $box = $(this);
      if ($box.is(":checked")) {
        // the name of the box is retrieved using the .attr() method
        // as it is assumed and expected to be immutable
        var group = "input:checkbox[name='" + $box.attr("name") + "']";
        // the checked state of the group/box on the other hand will change
        // and the current value is retrieved using .prop() method
        $(group).prop("checked", false);
        $box.prop("checked", true);
      } else {
        $box.prop("checked", false);
      }
    }
  });
</script>
</body>
</html>