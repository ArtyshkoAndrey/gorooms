@php
    $rating_categories = \Illuminate\Support\Facades\Cache::remember('rating_category', 60*60*24*12, function () {
        return \App\Models\RatingCategory::orderBy('sort')->get();
    })
@endphp
<div class="card-wrapper">
    <div class="room-card room-card-horizontal">
        <p class="room-card-name-top">Номер: {{ $room->name }}</p>
        <div class="rating">
            <p class="rating-title"><span class="rating-title-text">Рейтинг</span> <span>{{ round
            ($room->hotel->ratings->avg('value'),
             1) }}</span>
                ({{ count($room->hotel->reviews) }})</p>
            <div class="rating-dropdown">
                <div class="rating-dropdown-in">
                    <p class="rating-dropdown-header">{{ round(optional($room->hotel->ratings)->avg('value'), 1) }} Превосходно
                        <span>({{ count($room->hotel->reviews) }})</span></p>
                    <ul class="rating-dropdown-content">
                        @foreach($rating_categories AS $category)
                            @php
                                $rating = $room->hotel->ratings->where('category_id', $category->id)->avg('value');
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
        <div class="room-card-slider swiper-container js-hotel-card-slider">
            <div class="swiper-wrapper">
                @foreach($room->images AS $key => $image)
                    <div class="swiper-slide">
                        <img class="swiper-lazy" data-src="{{ asset($image->path) }}" alt="{{ $room->name }}" src="{{ asset('img/pr578x340.jpg') }}" title="{{ $room->name . ' | GoRooms.ru' . ($loop->iteration > 1 ? ' - фото ' . $loop->iteration : '') }}">
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
                <a href="{{ route('hotels.show', $room->hotel) }}" class="room-card-name">Отель
                    “{{ $room->hotel->name }}”</a>
                <button class="room-card-more" type="button">Подробнее</button>
                <p class="room-card-address">
                    <button type="button" class="room-address-link js-room-address-link"></button>
                    @php
                        $has_district = false;
                        $has_area = false;
                        $url = '/address/';
                        $hotel = $room->hotel;
                    @endphp
                    @if(isset($hotel->address->city) && !is_null($hotel->address->city))
                        @php
                            $url .= \Str::slug($hotel->address->city);
                        @endphp
                        <a href="{{ $url }}">{{ $hotel->address->city }}</a>,
                    @endif
                    @if(isset($hotel->address->city_area) && !is_null($hotel->address->city_area))
                        @php
                            $has_area = true;
                            $url .= '/area-'.\Str::slug($hotel->address->city_area);
                            $areas = explode('-', $hotel->address->city_area);
                            $area = '';
                            foreach ($areas AS $area_prefix)
                                $area .= mb_substr($area_prefix, 0, 1);
                            $area = mb_strtoupper($area).'АО';
                        @endphp
                        <a href="{{ $url }}">{{ $area }}</a>,
                    @endif
                    @if(isset($hotel->address->city_district) && !is_null($hotel->address->city_district))
                        @php
                            $has_district = true;
                            $url .= '/district-'.\Str::slug($hotel->address->city_district);
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
                            @php($hotel = $room->hotel)
                            @foreach ($hotel->metros as $metro)
                                @break($loop->index == 3)
                                <li class="metro">
                                    <a href="/address/{{ \Str::slug($hotel->address->city) }}/metro-{{ \Str::slug($metro->name) }}"><img
                                            src="{{ asset('/img/ico-metro-'.$metro->color.'.svg') }}"
                                            alt="">{{ $metro->name }} - {{ $metro->distance }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                <p class="room-card-attributes">
                    @foreach($room->attrs AS $attr)
                        <span>{{ $attr->name }}</span>
                    @endforeach
                </p>
            </div>
            <a href="{{ route('hotels.show', $room->hotel) }}#room-{{ $room->id }}">
                <ul class="room-card-prices">
                    @foreach($room->costs->sortBy('type.sort') AS $cost)
                        <li class="room-card-prices-item">
                            <div>
                                <p class="room-card-prices-item-title">{{ $cost->type->name }}</p>
                                <p class="room-card-prices-item-price">@if($cost->value !== '0'){{ $cost->value }}
                                    руб.@endif</p>
                            </div>
                            <p class="room-card-prices-item-time">{{ $cost->value === '0' ? 'не предоставляется' : $cost->description }}</p>
                        </li>
                    @endforeach
                </ul>
            </a>
            <div class="room-card-btns">
                <a href="" class="btn btn-orange btn-block" data-href="#book-popup" data-toggle="modal"
                   data-target="#book-popup" data-action="{{ route('booking.room', $room->id) }}"
                   onclick="showFormBookRoom('{{json_encode($room->id)}}')">Забронировать</a>
            </div>
        </div>
    </div>
</div>
