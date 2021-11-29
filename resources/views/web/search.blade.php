@extends('layouts.app')

@section('content')
  @include('web.parts.sections._search', ['search_link' => $with_map ? route('search.map') : null])
  @if($with_map)
    <div class="breadcrumbs">
      <div class="container">
        <ul itemscope itemtype="https://schema.org/BreadcrumbList">
          <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="/"> <span itemprop="name">Главная</span></a>
            <meta itemprop="position" content="1"/>
          </li>
          <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <span itemprop="name">¬ Результаты поиска</span>
            <meta itemprop="position" content="2"/>
          </li>
        </ul>
      </div>
    </div>
  @endif

  <section class="section">
    <div class="container">
      @if ($with_map)
        <h1 class="section-title">{!! @$pageDescription->title ?? $title !!}</h1>
        <div class="map-search-wrapper">
          <div id="map" style="width: 100%; height: 600px;"></div>
        </div>
      @else
        <div class="section-header">
          <h1 class="section-title">{!! @$pageDescription->title ?? $title !!}</h1>
          {{--<form id="search-filter" class="search-filter">
              <div class="search-filter-item search-filter-sort">
                  <button class="search-filter-price-btn" type="button"></button>
                  <button class="search-filter-price-btn" type="button"></button>
                  <label class="search-filter-label">Сортировать по цене</label>
              </div>
              <div class="search-filter-item">
                  <input type="checkbox" id="search-filter-popular" class="checkbox"
                         name="search-filter-popular">
                  <label for="search-filter-popular"
                         class="search-filter-label checkbox-label checkbox-label-dark">Популярные</label>
              </div>
              <div class="search-filter-item">
                  <label class="search-filter-label">Рейтинг отеля:</label>
                  <div class="hotel-rating">
                      <input type="radio" id="hotel-rating-any" name="hotel-rating" value="hotel-rating-any"
                             class="hotel-rating-input">
                      <label for="hotel-rating-any" class="hotel-rating-label">Любой</label>

                      <input type="radio" id="hotel-rating-6" name="hotel-rating" value="hotel-rating-6"
                             class="hotel-rating-input">
                      <label for="hotel-rating-6" class="hotel-rating-label">6+</label>

                      <input type="radio" id="hotel-rating-7" name="hotel-rating" value="hotel-rating-7"
                             class="hotel-rating-input">
                      <label for="hotel-rating-7" class="hotel-rating-label">7+</label>

                      <input type="radio" id="hotel-rating-8" name="hotel-rating" value="hotel-rating-8"
                             class="hotel-rating-input">
                      <label for="hotel-rating-8" class="hotel-rating-label">8+</label>

                      <input type="radio" id="hotel-rating-9" name="hotel-rating" value="hotel-rating-9"
                             class="hotel-rating-input">
                      <label for="hotel-rating-9" class="hotel-rating-label">9+</label>
                  </div>
              </div>
          </form>{{----}}
        </div>
      @endif
    </div>
  </section>

  <section class="section section-pt-none">
    @if($moderate ?? false)
      <div class="container">
          @foreach ($hotels as $hotel)
          <div class="row row-sm position-relative">
            <div class="col-sm-6 col-lg-3 col-xxl-2" style="position: relative">
              <div class="position-sticky" style="top: 20px; margin-bottom: 20px;position: sticky;">
                @include('hotel._popular', ['moderate' => true])
              </div>
            </div>
            <div class="col-sm-6 col-lg-9 col-xxl-10">
              <div class="row">
                @foreach ($hotel->rooms()->where('moderate', true)->get() as $room)
                 <div class="col-12">
                   @include('room._hot')
                 </div>
                @endforeach
              </div>
            </div>
          </div>
          @endforeach
      </div>
    @elseif($rooms)
      <div class="container">
        <div class="h2 section-title orange">Номера</div>
        <div class="items-container">
          @foreach ($rooms as $room)
            @include('room._hot')
          @endforeach

        </div>

      </div>
    @else
      <div class="container">
        @if($with_map)
          <div class="h2 section-title orange">Популярные отели</div>
        @endif
        <div class="row row-sm">
          @foreach ($hotels as $hotel)
            @continue(is_null($hotel) || ($with_map && !$hotel->is_popular))
            @include('hotel._popular')
          @endforeach
        </div>
      </div>
    @endif

    @if($rooms)
    <div class="container">
      <div class="show-more">
        <p class="show-more-counter">Загружено: {{ $rooms->count() }} ({{ $rooms->total()}})</p>
          @if($rooms->total() > $rooms->count())
            <button id="rooms-load-more" class="show-more-btn" type="button">Загрузить еще</button>
          @endif
      </div>
    </div>
    @endif
    
    @if($moderate ?? false)
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-auto">
            {{ $hotels->links() }}
          </div>
        </div>
      </div>
    @endif

    @if(!is_null(@$pageDescription))
      <div class="container" style="margin-top: 20px;">
        {!! html_entity_decode(@$pageDescription->description) !!}

      </div>

    @endif
  </section>
@section('scripts')
  <script>
    $(function () {
      let roomPageCount = 1;
      $('#rooms-address-load-more').click(async function (e) {
        roomPageCount++
        await loadMore(e, `{{Request::url()}}?page=${roomPageCount}`);
      });
    });
  </script>
@endsection
@if($with_map)
  <script>
    ymaps.ready(init);

    function init() {
      let myMap = new ymaps.Map("map", {
        center: [{{ $search_address['lat'] }}, {{ $search_address['lon'] }}],
        zoom: 9
      });

      @php($hotels_all_cached = \Illuminate\Support\Facades\Cache::remember('hotels_all', 60*60*24*12, function () {
          return \App\Models\Hotel::all();
      }))
      @foreach(($hotels ?? $hotels_all_cached) AS $hotel)
      @if($hotel->address)
      myMap.geoObjects.add(new ymaps.Placemark([{{ $hotel->address->geo_lat }}, {{ $hotel->address->geo_lon }}], {
        balloonContentBody: '<div class="map-popup-in">\n' +
          '                        <img src="{{ asset($hotel->image->path) }}" alt="" class="map-popup-img">\n' +
          '                        <div class="map-popup-content">\n' +
          '                            <p class="map-popup-title"><a href="{{ route('hotels.show', $hotel) }}">Отель “{{ $hotel->name }}”</a></p>\n' +
          '                            <a href="{{ Request::fullUrl().'&hotel='.$hotel->id }}" class="btn btn-blue map-popup-btn">Посмотреть номера</a>\n' +
          '                        </div>\n' +
          '                    </div>',
        hintContent: "Отель «{{ $hotel->name }}»"
      }));
      @endif
      @endforeach
    }
  </script>
@endif

@stop
