<?php
/**
 * @var \App\Models\Room $room;
 */
?>
<div class="card-wrapper">
    <div class="room-card room-card-horizontal">
        @include('room.parts._rating')
        <div class="room-card-slider swiper-container js-hotel-card-slider">
            <div class="swiper-wrapper">
                @foreach($room->images AS $image)
                    <a href="{{ route('rooms.show', $room) }}" class="swiper-slide">
                        <img class="swiper-lazy" data-src="{{ asset($image->path) }}" src="{{ asset('img/pr578x340.jpg') }}" alt="">
                    </a>
                @endforeach
            </div>
            <div class="swiper-button swiper-button-next"></div>
            <div class="swiper-button swiper-button-prev"></div>
        </div>
        <div class="room-card-in">
            <div class="room-card-content">
                <a href="{{ route('rooms.show', $room) }}" class="room-card-header">
                    <p class="room-card-title">Номер: {{ $room->name }}</p>
                    <p class="room-card-type">{{ optional($room->category)->name }}</p>
                </a>
                <a href="{{ route('hotels.show', $room->hotel) }}" class="room-card-name">Отель “{{ $room->hotel->name }}”</a>
                <a href="{{ route('rooms.show', $room) }}" class="room-card-address-link">
                    <p class="room-card-address">{{ optional($room->hotel->address)->value }}</p>
                    <ul class="room-card-metro">
                        <li class="metro"><img src="/img/ico-Fmetro-green.svg" alt="">Измайловская - 950 м</li>
                        <li class="metro"><img src="/img/ico-metro-red.svg" alt="">Измайловская - 950 м</li>
                        <li class="metro"><img src="/img/ico-metro-yellow.svg" alt="">Измайловская - 950 м</li>
                        <li class="metro"><img src="/img/ico-metro-blue.svg" alt="">Измайловская - 950 м</li>
                    </ul>
                </a>
            </div>F
            <a href="{{ route('rooms.show', $room) }}">
                <ul class="room-card-prices">
                    @foreach($room->costs->sortBy('period.type.sort') AS $cost)
                        <li class="room-card-prices-item">
                            <p class="room-card-prices-item-title">{{ $cost->period->type->name }} - от {{ $cost->value }}
                                руб.</p>
                            <p class="room-card-prices-item-price">{{ $cost->period->info }}</p>
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
            </div>
        </div>
    </div>
</div>
