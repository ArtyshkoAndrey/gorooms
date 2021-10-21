<form action="{{ $search_link ?? route('search') }}" id="js-advanced-search" class="advanced-search" method="GET">
    @php($hotels_attributes = \Illuminate\Support\Facades\Cache::remember('hotels_attributes', 60*60*24*12, function() {
        return \App\Models\Attribute::forHotels()->filtered()->get();
    }))
    @php($rooms_attributes = \Illuminate\Support\Facades\Cache::remember('rooms_attributes', 60*60*24*12, function () {
        return \App\Models\Attribute::forRooms()->filtered()->get();
    }))

    @if(@isset($address))
        <script>
            var SearchData = {
                "city": "{{ @$address['city'] ?? @$current_city ?? '' }}",
                "metro": "{{ @$address['metro'] ?? '' }}",
                "area": "{{ @$address['area'] ?? '' }}",
                "short_area": "{{ @$address['short_area'] ?? '' }}",
                "district": "{{ @$address['district'] ?? '' }}",
            }
        </script>
    @endif
    <div id="js-advanced-search-in" class="advanced-search-in">
        <div class="container">
            <div class="search-form-group">
                <input type="text" id="advanced-search" name="query" class="search-input"
                       placeholder="Название отеля, округ, район город, метро"
                       value="{{ old('query') ?? @$query ?? ''}}">
                <button class="btn btn-orange search-btn">Найти</button>
            </div>
            <div class="search-tags">
                @foreach ($hotels_attributes as $attribute)
                        @if(in_array($attribute->id, (isset($attributes['hotel']) ? $attributes['hotel'] : [])))
                            <div class="search-tag"><span>{{ $attribute->name }}</span><button data-for="advanced-search-hotel-{{ $attribute->id }}"></button></div>
                        @endif
                    @endforeach
                    @foreach ($rooms_attributes as $attribute)
                        @if(in_array($attribute->id, (isset($attributes['room']) ? $attributes['room'] : [])))
                            <div class="search-tag"><span>{{ $attribute->name }}</span><button data-for="advanced-search-rooms-{{ $attribute->id }}"></button></div>
                        @endif
                    @endforeach
            </div>
            <div class="search-dates">
             <!--   <div class="form-group-date">
                    <p class="form-group-date-label">Заезд:</p>
                    <input id="advanced-search-from-date" type="date" class="form-control form-control-date" placeholder="10.06.2020">
                    <input id="advanced-search-from-time" type="text" class="form-control form-control-time js-time" placeholder="14:00">
                </div>
                <div class="form-group-date">
                    <p class="form-group-date-label">Выезд:</p>
                    <input id="advanced-search-to-date" type="date" class="form-control form-control-date" placeholder="10.06.2020">
                    <input id="advanced-search-to-time" type="text" class="form-control form-control-time js-time" placeholder="14:00">
                </div>-->
            </div>
            <div class="row">
                <div class="col-xl-4 advanced-search-location-wrapper">
                    <div class="advanced-search-location">
                        <p class="advanced-search-title">Расположение <button class="js-search-btn-collapse search-btn-collapse" type="button"></button></p>
                        <div class="filter-collapse js-search-collapse">
                            <div class="form-group">
                                <input id="advanced-search-location-city"
                                       list="advanced-search-location-city-list"
                                       type="text"
                                       value="{{ @$address['city'] ?? @$current_city ?? '' }}"
                                       class="form-control form-control-sm flexdatalist"
                                       placeholder="Город"
                                       name="address[city]"
                                       autocomplete="off"
                                >
                                @if(isset($search_city))
                                <datalist id="advanced-search-location-city-list">
                                    @foreach ($search_city as $value)
                                        <option value="{{ $value }}">
                                    @endforeach
                                </datalist>
                                @endif
                            </div>
                            <div class="form-group">
                                <select id="advanced-search-location-district"
                                        name="address[city_area]"
                                        class="form-control"
                                        data-placeholder="Округ">
                                </select>
                            </div>
                            <div class="form-group" style="display: none;">
                                <select id="advanced-search-location-region"
                                        name="address[city_district]"
                                        class="form-control"
                                        data-placeholder="Район">
                                    <option value=""></option>
                                    {{--                                @foreach ($search_region as $value)--}}
                                    {{--                                    <option value="{{ $value }}">--}}
                                    {{--                                @endforeach--}}
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="advanced-search-location-metro"
                                        name="metro"
                                        class="form-control"
                                        data-placeholder="Метро">
                                    <option value=""></option>
                                    {{--                                @foreach ($search_region as $value)--}}
                                    {{--                                    <option value="{{ $value }}">--}}
                                    {{--                                @endforeach--}}
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="hotel_type" id="advanced-search-location-type"
                                        class="form-control"
                                        data-placeholder="Тип размещения">
                                    <option value=""></option>
                                    @php($hotels_type = \Illuminate\Support\Facades\Cache::remember('hotels_type', 60*60*24*12, function () {
                                        return \App\Models\HotelType::orderBy('sort')->get();
                                    }))
                                    @foreach ($hotels_type as $type)
                                        <option value="{{ $type->id }}"
                                                @if(isset($request) && optional($request)->get('hotel_type', -1) == $type->id) selected @endif>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-xl-8 advanced-search-prices-wrapper">
                    <div class="advanced-search-prices">
                        <p class="advanced-search-title">По цене
                            <button class="js-search-btn-collapse search-btn-collapse" type="button"></button></p>
                        <div class="filter-collapse js-search-collapse">
                            <div class="advanced-search-prices-wrap">
                                <div class="advanced-search-prices-in advanced-search-prices-control">
                                    <p class="advanced-search-prices-in-label">Период размещения:</p>
                                    <ul class="advanced-search-prices-list">
                                        <li class="advanced-search-prices-item ">
                                            <input id="advanced-search-prices-1" type="radio" class="checkbox" name="search-price" value="hour" @checked('hour', request('search-price'))>
                                            <label for="advanced-search-prices-1" class="checkbox-label checkbox-label-orange">На
                                                час</label>
                                        </li>
                                        <li class="advanced-search-prices-item">
                                            <input id="advanced-search-prices-2" type="radio" class="checkbox" name="search-price" value="night" @checked('night', request('search-price'))>
                                            <label for="advanced-search-prices-2" class="checkbox-label checkbox-label-orange">На
                                                ночь</label>
                                        </li>
                                        <li class="advanced-search-prices-item">
                                            <input id="advanced-search-prices-3" type="radio" class="checkbox" name="search-price" value="day" @checked('day', request('search-price'))>
                                            <label for="advanced-search-prices-3" class="checkbox-label checkbox-label-orange">На
                                                сутки</label>
                                        </li>
                                    </ul>
                                </div>
                                @php($request = isset($request) ? $request : null)
                                @foreach (['hour' => 'На час', 'night' => 'На ночь', 'day' => 'На сутки'] as $type => $title)
                                    <div class="advanced-search-prices-in advanced-search-prices-in-item {{ request('search-price') !== $type ? 'disabled' : ''}}">
                                        <p class="advanced-search-prices-in-label">{{ $title }}:</p>
                                        <ul class="advanced-search-prices-list">
                                            <li class="advanced-search-prices-item">
                                                @php($value = $type.'.lte.'.Settings::option($type.'_cost_small'))
                                                <input id="advanced-search-prices-{{ $loop->index }}-1" name="cost" type="radio" class="checkbox" value="{{$value}}" @checked($value, optional($request)->get('cost')) >
                                                <label for="advanced-search-prices-{{ $loop->index }}-1" class="checkbox-label checkbox-label-orange">до {{ Settings::option($type.'_cost_small') }} р.</label>
                                            </li>
                                            <li class="advanced-search-prices-item">
                                                @php($value = $type.'.between.'.Settings::option($type.'_cost_small').'-'.Settings::option($type.'_cost_medium'))
                                                <input id="advanced-search-prices-{{ $loop->index }}-2" name="cost" type="radio" class="checkbox" value="{{$value}}" @checked($value, optional($request)->get('cost')) >
                                                <label for="advanced-search-prices-{{ $loop->index }}-2" class="checkbox-label checkbox-label-orange">{{ Settings::option($type.'_cost_small') }} р.- {{ Settings::option($type.'_cost_medium') }} р</label>
                                            </li>
                                            <li class="advanced-search-prices-item">
                                                @php($value = $type.'.between.'.Settings::option($type.'_cost_medium').'-'.Settings::option($type.'_cost_low'))
                                                <input id="advanced-search-prices-{{ $loop->index }}-3" name="cost" type="radio" class="checkbox" value="{{$value}}" @checked($value, optional($request)->get('cost')) >
                                                <label for="advanced-search-prices-{{ $loop->index }}-3" class="checkbox-label checkbox-label-orange">{{ Settings::option($type.'_cost_medium') }} р. - {{ Settings::option($type.'_cost_low') }}
                                                    р</label>
                                            </li>
                                            <li class="advanced-search-prices-item">
                                                @php($value = $type.'.gte.'.Settings::option($type.'_cost_low'))
                                                <input id="advanced-search-prices-{{ $loop->index }}-4" name="cost" type="radio" class="checkbox" value="{{$value}}" @checked($value, optional($request)->get('cost')) >
                                                <label for="advanced-search-prices-{{ $loop->index }}-4" class="checkbox-label checkbox-label-orange">от {{ Settings::option($type.'_cost_low') }} р.</label>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="advanced-search-filter">
                <div class="left">
                    <div class="advanced-search-filter-item">
                        <p for="advanced-search-filter-profitably" class="search-filter-label search-filter-label-profitably">Выгодно</p>
                    </div>
                    <div class="advanced-search-filter-item">
                        <input type="checkbox" id="advanced-search-filter-fire" class="checkbox" name="hot" value="1"
                               @if(isset($request) && optional($request)->has('hot')) checked @endif
                        />
                        <label for="advanced-search-filter-fire"
                           class="search-filter-label search-filter-label-fire checkbox-label checkbox-label-light">Горящие
                            предложения</label>
                    </div>
                </div>
                <div class="right">
                    <div class="advanced-search-filter-item rating">
                        <p class="search-filter-label search-filter-label-profitably">Рейтинг отелей:</p>
                        <div class="rating-block">
                            <a href="#">Любой</a>
                            <a href="#">6+</a>
                            <a href="#">7+</a>
                            <a href="#">8+</a>
                            <a href="#">9+</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row advanced-search-details">
                <div class="col-lg-6 advanced-search-details-col">
                    <p class="advanced-search-title">Детально об отлеле <button class="js-search-btn-collapse search-btn-collapse" type="button"></button></p>
                    <div class="filter-collapse js-search-collapse">
                        <ul class="advanced-search-details-list">
    
                            @foreach ($hotels_attributes as $attribute)
                                <li class="advanced-search-details-item">
                                    <input id="advanced-search-hotel-{{ $attribute->id }}" type="checkbox"
                                           @if(in_array($attribute->id, (isset($attributes['hotel']) ? $attributes['hotel'] : [])))
                                           checked
                                           @endif
                                           name="attributes[hotel][]" value="{{ $attribute->id }}" class="checkbox">
                                    <label for="advanced-search-hotel-{{ $attribute->id }}"
                                           class="checkbox-label checkbox-label-light">{{ $attribute->name }}</label>
                                </li>
                            @endforeach

                            @moderator
                            <li class="advanced-search-details-item">
                                <input id="advanced-search-hotel-moderate" type="checkbox"
                                       @if(isset($request) && optional($request)->has('hotel_moderate')) checked @endif
                                       name="hotel_moderate" class="checkbox">
                                <label for="advanced-search-hotel-moderate"
                                       class="checkbox-label checkbox-label-light">На модерации</label>
                            </li>
                            @endmoderator
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 advanced-search-details-col">
                    <p class="advanced-search-title">Детально о номерах <button class="js-search-btn-collapse search-btn-collapse" type="button"></button></p>
                    <div class="filter-collapse js-search-collapse">
                        <ul class="advanced-search-details-list">
    
                            @foreach ($rooms_attributes as $attribute)
                                <li class="advanced-search-details-item">
                                    <input id="advanced-search-rooms-{{ $attribute->id }}" type="checkbox"
                                           @if(in_array($attribute->id, (isset($attributes['room']) ? $attributes['room'] : [])))
                                           checked
                                           @endif
                                           name="attributes[room][]" value="{{ $attribute->id }}" class="checkbox">
                                    <label for="advanced-search-rooms-{{ $attribute->id }}"
                                           class="checkbox-label checkbox-label-light">{{ $attribute->name }}</label>
                                </li>
                            @endforeach

                            @moderator
                                <li class="advanced-search-details-item">
                                    <input id="advanced-search-rooms-moderate" type="checkbox"
                                           @if(isset($request) && optional($request)->has('room_moderate')) checked @endif
                                           name="room_moderate" class="checkbox">
                                    <label for="advanced-search-rooms-moderate"
                                           class="checkbox-label checkbox-label-light">На модерации</label>
                                </li>
                            @endmoderator
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="search-bottom">
        <div class="search-bottom-in">
            <button class="advanced-search-ico-btn advanced-search-ico-btn-favorite" type="button"></button>
            <a href="{{ route('search.map') }}" class="advanced-search-ico-btn advanced-search-ico-btn-map"
               type="button"></a>
            <button  onclick="event.preventDefault();search_reset();" class="advanced-search-reset-btn">Очистить поиск</button>
            <button class="btn btn-blue" type="submit">Показать</button>
        </div>
        <button id="js-advanced-search-close-btn" type="button"
                class="advanced-search-btn advanced-search-btn-close">Свернуть фильтры
        </button>
    </div>
</form>
<div id="js-search-wrapper" class="search-wrapper">
    <div id="js-search" class="search">
        <div class="container">
            <form action="{{ $search_link ?? route('search') }}" id="search-form" class="search-form" method="GET">
                <div class="search-form-group">
                    <input type="search"
                           id="search"
                           name="query"
                           class="search-input"
                           value="{{ old('query') ?? @$query ?? '' }}"
                           placeholder="Название отеля, округ, район город, метро">
                    <button class="btn btn-orange search-btn">Найти</button>
                </div>
                <div class="search-tags">
                    @foreach ($hotels_attributes as $attribute)
                        @if(in_array($attribute->id, (isset($attributes['hotel']) ? $attributes['hotel'] : [])))
                            <div class="search-tag"><span>{{ $attribute->name }}</span><button data-for="advanced-search-hotel-{{ $attribute->id }}"></button></div>
                        @endif
                    @endforeach
                    @foreach ($rooms_attributes as $attribute)
                        @if(in_array($attribute->id, (isset($attributes['room']) ? $attributes['room'] : [])))
                            <div class="search-tag"><span>{{ $attribute->name }}</span><button data-for="advanced-search-rooms-{{ $attribute->id }}"></button></div>
                        @endif
                    @endforeach
                </div>
                <div class="search-bottom">
                   <!-- <div class="search-dates">
                        <div class="form-group-date">
                            <p class="form-group-date-label">Заезд:</p>
                            <input id="search-from-date" type="date" class="form-control form-control-date" placeholder="10.06.2020">
                            <input id="search-from-time" type="text" class="form-control form-control-time js-time" placeholder="14:00">
                        </div>
                        <div class="form-group-date">
                            <p class="form-group-date-label">Выезд:</p>
                            <input id="search-to-date" type="date" class="form-control form-control-date" placeholder="10.06.2020">
                            <input id="search-to-time" type="text" class="form-control form-control-time js-time" placeholder="14:00">
                        </div>-->
                    </div>
                    <button id="js-advanced-search-open-btn" class="advanced-search-btn advanced-search-btn-open">
                        Расширеный поиск
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
