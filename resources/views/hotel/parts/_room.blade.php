<?php
/**
 * @var Room $room ;
 */

use App\Models\Room;

?>


@push('header')

  <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Apartment",
        "name": "Отель “{{ $room->hotel->name }}”",
        "description": "Отель “{{ $room->hotel->name }}”",
        "numberOfRooms": 1,
        "occupancy": {
            "@type": "QuantitativeValue",
            "minValue": 1,
            "maxValue": 2
        },
        "floorLevel": "{{ $hotel->address->comment }}",
        "floorSize": {
            "@type": "QuantitativeValue",
            "value":  1,
            "unitCode": " "
        },
        "numberOfBathroomsTotal": 1,
        "numberOfBedrooms": 1,
        "petsAllowed": false,
        "tourBookingPage": "https://gorooms.ru/",
        "yearBuilt": 23,
        "telephone": "{{ Settings::option('phone') }}",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "Россия",
            "addressLocality": "{{ $hotel->address->city_district }} район",
            "addressRegion": "{{ $hotel->address->city }}",
            "postalCode": "0",
            "streetAddress": "{{ $hotel->address->block }}"
        },
        "latitude": 37.622504,
        "longitude": 55.753215
    }

        
  


  </script>

@endpush

<div class="card-wrapper" id="room-{{ $room->id }}">
  <a name="room-{{ $room->id }}"></a>
  <div class="room-card room-card-horizontal">
    <p class="room-card-name-top">Номер: {{ $room->name }}</p>
    @include('room.parts._rating')
    <div class="room-card-slider swiper-container js-hotel-card-slider">
      <div class="swiper-wrapper">
        @foreach($room->images AS $image)
          <div class="swiper-slide">
            <img class="swiper-lazy" data-src="{{ asset($image->path) }}?w=578&h=340&fit=crop&fm=webp&q=85"
                 src="{{ asset('img/pr578x340.jpg') }}" alt="">
          </div>
        @endforeach
      </div>
      <div class="swiper-button swiper-button-next"></div>
      <div class="swiper-button swiper-button-prev"></div>
    </div>
    <div class="room-card-in">
      <div class="room-card-content">
        <a href="{{ route('hotels.show', $room->hotel) }}#room-{{ $room->id }}" class="room-card-header">
          <p class="room-card-title">Номер: {{ $room->name }}</p>
          <p class="room-card-type">{{ optional($room->category)->name }}</p>
        </a>
        <button class="room-card-more" type="button">Подробнее</button>
        <a href="{{ route('hotels.show', $room->hotel) }}" class="room-card-name">Отель “{{ $room->hotel->name }}”</a>
        <p class="room-card-address">
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
            <a href="{{ $url }}">{{ $hotel->address->city }}</a>,
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
            <a href="{{ $url }}">{{ $area }}</a>,
          @endif
          @if(isset($hotel->address->city_district) && !is_null($hotel->address->city_district))
            @php
              $has_district = true;
              $url .= '/district-'.Str::slug($hotel->address->city_district)
            @endphp
            <a href="{{ $url }}">{{ $hotel->address->city_district }} район</a>,
          @endif
          @if(isset($hotel->address->street) && !is_null($hotel->address->street))
            {{ $hotel->address->street_with_type ?? $hotel->address->street }}@isset($hotel->address->house)
              , д.{{ $hotel->address->house }} @endisset
            @isset($hotel->address->block), к.{{ $hotel->address->block }} @endisset
          @endif {{ $hotel->address->comment ? ', '.$hotel->address->comment : '' }}
        </p>
        @if(count($room->hotel->metros))
          <ul class="room-card-metro">
            @foreach ($room->hotel->metros as $metro)
              <li class="metro">
                <a href="/address/{{ Str::slug($hotel->address->city) }}/metro-{{ \Str::slug($metro->name) }}">
                  <i class="icon-metro mr-2" style="color: #{{ $metro->color }}"></i>
                  {{ $metro->name }} - {{ $metro->distance }} мин <img class="svg-walk" src="{{asset('img/walk.svg')}}" alt="">
                </a>
              </li>
            @endforeach
          </ul>
        @endif
        <p class="room-card-attributes">
          @foreach($room->attrs AS $attr)
            <span class="attribute">{{ $attr->name }}</span>@if(!$loop->last) @endif
          @endforeach
        </p>
      </div>
      <a href="{{ route('hotels.show', $room->hotel) }}#room-{{ $room->id }}">
        <ul class="room-card-prices">
          @foreach($room->all_costs AS $cost)
            <li class="room-card-prices-item">
              @if(isset($cost->period))
                <div>
                  <p class="room-card-prices-item-title">{{ $cost->period->type->name }}</p>
                  <p class="room-card-prices-item-price">
                    @if($cost->value !== '0'){{ $cost->value }} руб.@endif</p>
                  @if(isset($cost->period->info))
                    {{$cost->period->info}}
                  @endif
                </div>
              @else
                <div>
                  <p class="room-card-prices-item-title">{{ $cost->type->name }}</p>
                  <p class="room-card-prices-item-price">{{ $cost->value }}</p>
                </div>
              @endif
            </li>
          @endforeach
        </ul>
      </a>
      <div class="room-card-btns">
        <a href="" class="btn btn-orange btn-block" data-href="#book-popup" data-toggle="modal"
           data-target="#book-popup" data-action="{{ route('booking.room', $room->id) }}"
           onclick="showFormBookRoom('{{$room->id}}')">Забронировать</a>
           @if ($room->phone || $room->hotel->phone)
                            <p class="show-tel-wrapper">
                                <button class="btn btn-blue btn-block js-show-tel-btn" type="button">Показать телефон
                                </button>
                                <a href="tel:{{ $room->phone ?? $room->hotel->phone }}"
                                   class="btn btn-blue btn-block tel-link js-tel-link">{{ $room->phone ?? $room->hotel->phone }}</a>
                            </p>
                        @endif

        @moderator
          <a href="{{ route('moderator.room.edit', $hotel->id) }}" class="btn btn-orange btn-block btn-moderator-edit">Редактировать</a>
        @endmoderator
      </div>
    </div>
  </div>

</div>
