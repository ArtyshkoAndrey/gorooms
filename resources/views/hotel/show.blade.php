@extends('layouts.app')
<?php
/** @var Hotel $hotel */
use App\Models\Hotel;
?>

@push('header')
  <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "{{ $hotel->name }}",
      "image": [
        @foreach ($hotel->images as $image)
      "{{ asset($image->path) }}"{{ $loop->last ? '' : ',' }}
    @endforeach
    ],
   "description": "{{ strip_tags(html_entity_decode($hotel->description)) }}",
      "review": {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
        "worstRating":"0",
          "ratingValue": "{{ round($hotel->ratings->avg('value'), 1) }}",
          "bestRating": "{{ $hotel->ratings->max('value') ?? 0 }}"
        }
        @if($hotel->reviews()->count()),
        "author": {
          "@type": "Person",
          "name": "{{ $hotel->reviews()->latest()->first()->name }}"
        }
        @else,
        "author": {
          "@type": "Person",
          "name": ""
        }
         @endif
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "bestRating":"0",
      "worstRating":"0",
      "ratingValue": "{{ round($hotel->ratings->avg('value'), 1) }}",
            @if($hotel->reviews()->count() !=0),
            "reviewCount": "{{ $hotel->reviews()->count() }}"
            @else
      "reviewCount": "1"
@endif

    },
    "hasOfferCatalog": {
      "@type": "OfferCatalog",
      "name": "Услуги отеля",
      "itemListElement": [
@foreach($hotel->attrs AS $attr)
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "name": "{{ $attr->name }}"
            }
          }{{ $loop->last ? '' : ',' }}
    @endforeach
    ]
  }
}


  </script>
@endpush

@section('content')
  <div class="breadcrumbs">
    <div class="container">
      <ul itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($Breadcrumbs_din as $item)
          <li itemid="http://schema.org/breadcrumb" itemprop="itemListElement" itemscope
              itemtype="https://schema.org/ListItem">
            <a id="breadcrumbs" itemscope itemtype="http://schema.org/Thing" itemprop="item"
               href="/{{$item['url']}}"><span itemprop="name">{{$item['text']}}</span></a>
            <meta itemprop="position" content="{{ $loop->index + 1 }}"/>
          </li>
        @endforeach
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <span itemprop="name">{{ $hotel->name }}</span>
          <meta itemprop="position" content="{{ count($Breadcrumbs_din)+1 }}"/>
        </li>
      </ul>
    </div>
  </div>
  <section class="section">
    <div class="container">
      <div class="row">
        <div class="col-lg-5 col-xxl-6">
          <div class="hotel-content">
            <div class="hotel-content-header">
              <h1 class="section-title" itemprop="name">{{ $hotel->type->single_name }}: {{ $hotel->name }}</h1>
              <p class="room-address" itemprop="address">
                <button type="button" class="room-address-link js-room-address-link"></button>
                @php
                  $has_district = false;
                  $has_area = false;
                  $url = '/address/'
                @endphp
                @if(isset($hotel->address->city) && !is_null($hotel->address->city))
                  @php
                    $url .= Str::slug($hotel->address->city)
                  @endphp
                  <a itemprop="addressRegion" href="{{ $url }}">{{ $hotel->address->city }}</a>,
                @endif
                @if(isset($hotel->address->city_area) && !is_null($hotel->address->city_area))
                  @php
                    $has_area = true;
                    $url .= '/area-'.Str::slug($hotel->address->city_area);
                    $areas = explode('-', $hotel->address->city_area);
                    $area = '';
                    foreach ($areas AS $area_prefix)
                        $area .= mb_substr($area_prefix, 0, 1);
                    $area = mb_strtoupper($area).'АО'
                  @endphp
                  <a itemprop="addressLocality" href="{{ $url }}">{{ $area }}</a>,
                @endif
                @if(isset($hotel->address->city_district) && !is_null($hotel->address->city_district))
                  @php
                    $has_district = true;
                    $url .= '/district-'.Str::slug($hotel->address->city_district)
                  @endphp
                  <a href="{{ $url }}">{{ $hotel->address->city_district }} район</a>,
                @endif
                <span itemprop="streetAddress">
                                @if(isset($hotel->address->street) && !is_null($hotel->address->street))
                    {{ $hotel->address->street_with_type ?? $hotel->address->street }}@isset($hotel->address->house)
                      , д.{{ $hotel->address->house }} @endisset
                    @isset($hotel->address->block), к.{{ $hotel->address->block }} @endisset
                  @endif {{ $hotel->address->comment ? ', '.$hotel->address->comment : '' }}
                                <span>
              </p>
              <div class="block-desktop metro-rwd">
                <ul class="room-metro">
                  @if ($hotel->metros()->count())
                    @foreach ($hotel->metros as $metro)
                      <li class="metro">
                        <a href="/address/{{ Str::slug($hotel->address->city) }}/metro-{{ Str::slug($metro->name) }}">
                          <i class="icon-metro mr-2" style="color: #{{ $metro->color }}"></i>
                          {{ $metro->name }} - {{ $metro->distance }} мин <img class="svg-walk" src="{{asset('img/walk.svg')}}" alt="">
                        </a>
                      </li>
                    @endforeach

                  @endif
                </ul>
              </div>
            </div>

            <ul class="room-prices block-desktop">
              @foreach($hotel->minimals AS $minimal)
                <li class="room-prices-item" itemprop="priceRange">
                  @if($minimal->value !== 0)
                    <strong class="room-prices-item-price">{{ $minimal->name ?? '' }}
                       -
                      от {{ $minimal->value ?? '' }} руб.</strong>
                    <span class="room-prices-item-info">{{ $minimal->info ?? '' }}</span>
                  @else
                    <strong class="room-prices-item-price">{{ $minimal->name ?? '' }}
                      - {{ $minimal->info ?? '' }}</strong>
                  @endif
                </li>
              @endforeach
            </ul>
          </div>
        </div>
        <div class="col-lg-7 col-xxl-6 block-desktop">
          <div class="product-slider-wrapper hotel-product-slider-wrapper">
            <div class="swiper-container product-slider-big">
              <div class="swiper-wrapper">
                <div class="swiper-slide product-slide-big">
                  @if($hotel->images()->count() > 0)
                  <img itemprop="photo" class="swiper-lazy"
                       data-src="{{ asset($hotel->images[0]->path) }}?w=640&h=300&fit=crop&fm=webp"
                       src="{{ $hotel->images[0]->path }}" alt="">
                    @endif
                </div>
              </div>
            </div>
            <div class="product-slider-small-wrapper">
              <div class="swiper-container product-slider-small">
                <div class="swiper-wrapper">
                  @foreach ($hotel->images as $image)
                    <div class="swiper-slide product-slide-small" data-full="{{ asset($image->path) }}?w=640&h=300&fit=crop&fm=webp">
                      <img itemprop="photo" class="swiper-lazy"
                           data-src="{{ asset($image->path) }}?w=125&h=85&fit=crop&fm=webp"
                           src="{{ asset('img/pr125x85.jpg') }}" alt="">
                    </div>
                  @endforeach
                </div>
              </div>
              <div class="swiper-button swiper-button-next product-slider-small-button-next"></div>
              <div class="swiper-button swiper-button-prev product-slider-small-button-prev"></div>
            </div>
            <div class="rating">
              <p class="rating-title" itemprop="ratingValue">Рейтинг
                <span>{{ round($hotel->ratings()->avg('value'), 1) }}</span>
                ({{ $hotel->reviews()->count() }})</p>
              <div class="rating-dropdown">
                <div class="rating-dropdown-in">
                  <p class="rating-dropdown-header">{{ round($hotel->ratings()->avg('value'), 1) }}
                    Превосходно <span>({{ $hotel->reviews()->count() }})</span></p>
                  <ul class="rating-dropdown-content">
                    @foreach(\App\Models\RatingCategory::orderBy('sort')->get() AS $category)
                      @php
                        $rating = $hotel->ratings()->where('category_id', $category->id)->avg('value')
                      @endphp
                      <li class="rating-dropdown-item">
                        <span>{{ round($rating, 1) }}</span> {{ $category->name }}
                      </li>
                    @endforeach
                    <li>
                      <a href="#reviews" data-toggle="tab">Читать отзывы</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section block-mobile">
    <div class="container">
      @php
        $is_room = !isset($hotel) && isset($room);
        $attrs = $is_room ? $room->attrs : $hotel->attrs
      @endphp
      <div class="h2 section-title">
        <a class="room-card-more js-details-list-btn" href="#details-list-wrapper-m" role="button"
           data-toggle="collapse" aria-expanded="false"
           aria-controls="details-list-wrapper-m">Детально об {{ $is_room ? 'номере' : 'отеле' }}</a>
      </div>
      <div id="details-list-wrapper-m" class="catalog-filter collapse" aria-expanded="true"
           role="tabpanel">
        <ul class="room-metro">
          @if ($hotel->metros()->count())
            @foreach ($hotel->metros as $metro)
              <li class="metro">
                <a href="/address/{{ Str::slug($hotel->address->city) }}/metro-{{ Str::slug($metro->name) }}">
                  <i class="icon-metro mr-2" style="color: #{{ $metro->color }}"></i>
                  {{ $metro->name }} - {{ $metro->distance }} мин <img class="svg-walk" src="{{asset('img/walk.svg')}}" alt="">
                </a>
              </li>
            @endforeach

          @endif
        </ul>
        <ul class="room-prices">
          @foreach($hotel->minimals AS $minimal)
            <li class="room-prices-item">
              @if($minimal->value !== 0)
                <strong class="room-prices-item-price">{{ $minimal->name ?? '' }}
                  -
                  от {{ $minimal->value ?? '' }} руб.</strong>
                <span class="room-prices-item-info">{{ $minimal->info ?? '' }}</span>
              @else
                <strong class="room-prices-item-price">{{ $minimal->name ?? '' }}
                  - {{ $minimal->info ?? '' }}</strong>
              @endif
            </li>
          @endforeach
        </ul>
        <div class="product-slider-wrapper hotel-product-slider-wrapper">
          <div class="swiper-container product-slider-big-mobile">
            <div class="swiper-wrapper">
              @foreach ($hotel->images as $image)
                <a href="{{ asset($image->path) }}" data-fancybox="gallery-m"
                   class="swiper-slide product-slide-big">
                  <img class="swiper-lazy" data-src="{{ asset($image->path) }}?w=640&h=300&fit=crop&fm=webp"
                       src="{{ asset('img/pr600x300.jpg') }}" alt="">
                </a>
              @endforeach
            </div>
          </div>
          <div class="rating">
            <p class="rating-title">Рейтинг
              <span>{{ round($hotel->ratings()->avg('value'), 1) }}</span>
              ({{ $hotel->reviews()->count() }})</p>
            <div class="rating-dropdown">
              <div class="rating-dropdown-in">
                <p class="rating-dropdown-header">{{ round($hotel->ratings()->avg('value'), 1) }}
                  Превосходно <span>({{ $hotel->reviews()->count() }})</span></p>
                <ul class="rating-dropdown-content">
                  @foreach(\App\Models\RatingCategory::orderBy('sort')->get() AS $category)
                    @php
                      $rating = $hotel->ratings()->where('category_id', $category->id)->avg('value')
                    @endphp
                    <li class="rating-dropdown-item">
                      <span>{{ round($rating, 1) }}</span> {{ $category->name }}
                    </li>
                  @endforeach
                  <li>
                    <a href="#reviews" data-toggle="tab">Читать отзывы</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <ul class="details-list">
          @foreach ($attrs as $attr)
            <li>{{ $attr->name }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  </section>

  @include('web.parts.sections._details')

  <section class="section">
    <div class="container">
      <ul class="nav nav-tabs hotel-tabs">
        <li class="hotel-tabs-item active">
          <a href="#rooms" data-toggle="tab">Номера</a>
        </li>
        <li class="hotel-tabs-item">
          <a href="#info" data-toggle="tab">Общая информация</a>
        </li>
        <li class="hotel-tabs-item">
          <a href="#route" data-toggle="tab">Как добраться</a>
        </li>
        <li class="hotel-tabs-item">
          <a href="#reviews" data-toggle="tab">Отзывы</a>
        </li>
        @moderator
          <li class="hotel-tabs-item">
            <a href="{{ route('moderator.object.edit', $hotel->id) }}">Редактировать</a>
          </li>
        @endmoderator
      </ul>
      <div class="tab-content hotel-tab-content">
        <div id="rooms" class="tab-pane fade in active">
          @foreach($hotel->rooms AS $room)
            @include('hotel.parts._room')
          @endforeach
          <div class="show-more">
            <p class="show-more-counter">
              Загружено: {{ \App\Models\Room::PER_PAGE < $hotel->rooms()->count() ? \App\Models\Room::PER_PAGE : $hotel->rooms()->count() }}
              ({{ $hotel->rooms()->count() }})</p>
            @if ($hotel->rooms()->count() > \App\Models\Room::PER_PAGE)
              <button id="hotel-page-load-more" class="show-more-btn" type="button">Загрузить еще</button>
            @endif
          </div>
          <div class="text-section">
            {!! html_entity_decode($hotel->description) !!}
          </div>
        </div>
        <div id="info" class="tab-pane fade">
          <div class="text-section">
            {!! html_entity_decode($hotel->description) !!}
          </div>
        </div>
        <div id="route" class="tab-pane fade">
          <div class="row">
            <div class="col-lg-7 order-lg-2 hotel-map-wrapper">
              <div id="map" style="height: 424px; width: 100%;"></div>
              <script>
                ymaps.ready(init);

                function init() {
                  let myMap = new ymaps.Map("map", {
                    center: [{{ $hotel->address->geo_lat }}, {{ $hotel->address->geo_lon }}],
                    zoom: 14
                  });
                  myMap.geoObjects.add(new ymaps.Placemark([{{ $hotel->address->geo_lat }}, {{ $hotel->address->geo_lon }}], {}));
                }
              </script>
            </div>
            <div class="col-lg-5 order-lg-1 text-section">
              <div class="h2 section-title">{{ $hotel->route_title }}</div>
              {!! strip_tags(html_entity_decode($hotel->route)) !!}
            </div>
          </div>
        </div>
        <div id="reviews" class="tab-pane fade">
          <div class="total-rating">
            <div class="total-rating-main">
              <span>{{ round($hotel->ratings->avg('value'), 1) }}</span>
              <span>рейтинг</span>
            </div>
            <ul class="total-rating-list">
              @foreach(\App\Models\RatingCategory::orderBy('sort')->get() AS $category)
                <li class="total-rating-item">{{ $category->name }}
                  <span>{{ round($hotel->ratings()->where('category_id', $category->id)->avg('value'), 1) }}</span>
                  ({{ $hotel->reviews->count() }})
                </li>
              @endforeach
            </ul>
          </div>
          {{-- Форма для отправки отзывов --}}
          {{-- <form id="review-form" class="review-form">
              <h2 class="section-title review-form-mobile-title">Оставить отзыв об отеле
                  <a href="#review-form-wrapper" class="review-form-btn" role="button" data-toggle="collapse" aria-expanded="false"
                     aria-controls="review-form-wrapper"></a></h2>
              <div id="review-form-wrapper" class="collapse in" aria-expanded="true" role="tabpanel">
                  <div class="row">
                      <div class="col-lg-7 col-xl-6">
                          <h2 class="section-title review-form-desktop-title black">Оставить отзыв об отеле</h2>
                          <div class="stars-wrapper">
                              <div class="stars">
                                  <input id="star-rating-1-10" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-10"></label>

                                  <input id="star-rating-1-9" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-9"></label>

                                  <input id="star-rating-1-8" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-8"></label>

                                  <input id="star-rating-1-7" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-7"></label>

                                  <input id="star-rating-1-6" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-6"></label>

                                  <input id="star-rating-1-5" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-5"></label>

                                  <input id="star-rating-1-4" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-4"></label>

                                  <input id="star-rating-1-3" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-3"></label>

                                  <input id="star-rating-1-2" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-2"></label>

                                  <input id="star-rating-1-1" type="radio" name="reviewStars1">
                                  <label for="star-rating-1-1"></label>
                              </div>
                              <p class="stars-title"><span>9.7</span>Чистота</p>
                          </div>
                          <div class="stars-wrapper">
                              <div class="stars">
                                  <input id="star-rating-2-10" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-10"></label>

                                  <input id="star-rating-2-9" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-9"></label>

                                  <input id="star-rating-2-8" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-8"></label>

                                  <input id="star-rating-2-7" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-7"></label>

                                  <input id="star-rating-2-6" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-6"></label>

                                  <input id="star-rating-2-5" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-5"></label>

                                  <input id="star-rating-2-4" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-4"></label>

                                  <input id="star-rating-2-3" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-3"></label>

                                  <input id="star-rating-2-2" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-2"></label>

                                  <input id="star-rating-2-1" type="radio" name="reviewStars2">
                                  <label for="star-rating-2-1"></label>
                              </div>
                              <p class="stars-title"><span>9.7</span>Общий комфорт</p>
                          </div>
                          <div class="stars-wrapper">
                              <div class="stars">
                                  <input id="star-rating-3-10" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-10"></label>

                                  <input id="star-rating-3-9" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-9"></label>

                                  <input id="star-rating-3-8" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-8"></label>

                                  <input id="star-rating-3-7" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-7"></label>

                                  <input id="star-rating-3-6" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-6"></label>

                                  <input id="star-rating-3-5" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-5"></label>

                                  <input id="star-rating-3-4" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-4"></label>

                                  <input id="star-rating-3-3" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-3"></label>

                                  <input id="star-rating-3-2" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-2"></label>

                                  <input id="star-rating-3-1" type="radio" name="reviewStars3">
                                  <label for="star-rating-3-1"></label>
                              </div>
                              <p class="stars-title"><span>9.7</span>Персонал</p>
                          </div>
                          <div class="stars-wrapper">
                              <div class="stars">
                                  <input id="star-rating-4-10" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-10"></label>

                                  <input id="star-rating-4-9" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-9"></label>

                                  <input id="star-rating-4-8" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-8"></label>

                                  <input id="star-rating-4-7" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-7"></label>

                                  <input id="star-rating-4-6" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-6"></label>

                                  <input id="star-rating-4-5" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-5"></label>

                                  <input id="star-rating-4-4" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-4"></label>

                                  <input id="star-rating-4-3" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-3"></label>

                                  <input id="star-rating-4-2" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-2"></label>

                                  <input id="star-rating-4-1" type="radio" name="reviewStars4">
                                  <label for="star-rating-4-1"></label>
                              </div>
                              <p class="stars-title"><span>9.7</span>Удобства</p>
                          </div>
                          <div class="stars-wrapper">
                              <div class="stars">
                                  <input id="star-rating-5-10" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-10"></label>

                                  <input id="star-rating-5-9" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-9"></label>

                                  <input id="star-rating-5-8" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-8"></label>

                                  <input id="star-rating-5-7" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-7"></label>

                                  <input id="star-rating-5-6" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-6"></label>

                                  <input id="star-rating-5-5" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-5"></label>

                                  <input id="star-rating-5-4" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-4"></label>

                                  <input id="star-rating-5-3" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-3"></label>

                                  <input id="star-rating-5-2" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-2"></label>

                                  <input id="star-rating-5-1" type="radio" name="reviewStars5">
                                  <label for="star-rating-5-1"></label>
                              </div>
                              <p class="stars-title"><span>9.7</span>Расположение</p>
                          </div>
                          <div class="stars-wrapper">
                              <div class="stars">
                                  <input id="star-rating-6-10" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-10"></label>

                                  <input id="star-rating-6-9" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-9"></label>

                                  <input id="star-rating-6-8" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-8"></label>

                                  <input id="star-rating-6-7" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-7"></label>

                                  <input id="star-rating-6-6" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-6"></label>

                                  <input id="star-rating-6-5" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-5"></label>

                                  <input id="star-rating-6-4" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-4"></label>

                                  <input id="star-rating-6-3" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-3"></label>

                                  <input id="star-rating-6-2" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-2"></label>

                                  <input id="star-rating-6-1" type="radio" name="reviewStars6">
                                  <label for="star-rating-6-1"></label>
                              </div>
                              <p class="stars-title"><span>9.7</span>Цена</p>
                          </div>
                      </div>
                      <div class="col-lg-5 col-xl-6">
                          <div class="form-group">
                              <input id="review-name" name="review-name" type="text" class="form-control form-control-border"
                                     placeholder="Имя*" required>
                          </div>
                          <div class="form-group">
                              <input id="review-city" name="review-city" type="text" class="form-control form-control-border"
                                     placeholder="Город*" required>
                          </div>
                          <div class="form-group">
                              <input id="review-book-number" name="review-book-number" type="text" class="form-control form-control-border"
                                     placeholder="Номер бронирования*" required>
                          </div>
                          <div class="form-group">
                          <textarea name="review-comment" id="review-comment" class="form-control form-control-border"
                                    placeholder="Текст отзыва*"></textarea>
                          </div>
                          <button class="btn btn-block btn-blue">Отправить</button>
                      </div>
                  </div>
              </div>
          </form> --}}
          @foreach($hotel->reviews AS $review)
            @include('hotel.parts._review')
          @endforeach
          <div class="show-more">
            <p class="show-more-counter">Загружено: {{ \App\Models\Review::PER_PAGE }}
              ({{ $hotel->reviews()->count() }})</p>
            <button id="review-show-more" class="show-more-btn" type="button">Все отзывы</button>
          </div>
        </div>
      </div>
    </div>
  </section>
@stop
