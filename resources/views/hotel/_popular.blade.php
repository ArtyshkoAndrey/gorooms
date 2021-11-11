@php
  use App\Models\RatingCategory;use Illuminate\Support\Facades\Cache;$rating_categories = Cache::remember('rating_category', 60*60*24*12, function () {
      return RatingCategory::orderBy('sort')->get();
  })
@endphp
@if ($moderate ?? false)
  <div class="col-p-sm col-12 card-wrapper">
@else
<div class="col-p-sm col-sm-6 col-lg-3 col-xxl-2 card-wrapper">
@endif
  <div class="hotel-card">
    <a href="{{ route('hotels.show', $hotel) }}" class="hotel-card-title-top" target="_blank">{{ $hotel->name }}</a>
    <div class="rating">
      <p class="rating-title"><span class="rating-title-text">Рейтинг</span>
        <span>{{ round(optional($hotel->ratings)->avg('value'), 1) }}</span>
        ({{ optional($hotel->review)->count() ?? 0 }})</p>
      <div class="rating-dropdown">
        <div class="rating-dropdown-in">
          <p class="rating-dropdown-header">{{ round(optional($hotel->ratings)->avg('value'), 1) }}
            Превосходно <span>({{ count($hotel->reviews) }})</span></p>
          <ul class="rating-dropdown-content">
            @foreach($rating_categories AS $category)
              @php
                $rating = $hotel->ratings->where('category_id', $category->id)->avg('value')
              @endphp
              <li class="rating-dropdown-item">
                <span>{{ round($rating, 1) }}</span> {{ $category->name }}
              </li>
            @endforeach
            <li>
              <a href="{{ route('hotels.show', $hotel) }}#reviews">Читать отзывы</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="hotel-card-slider swiper-container {{$hotel->moderate ? '' : 'js-hotel-card-slider'}}">
      @if($hotel->moderate)
{{--        <div class="swiper-wrapper">--}}
          <a href="#!" class="swiper-slide">
            <img src="{{ asset('img/hotel-moderate.jpg') }}" alt="moderate">
          </a>
{{--        </div>--}}
      @else
        <div class="swiper-wrapper">
          @foreach($hotel->images AS $image)
            <a href="{{ route('hotels.show', $hotel) }}" class="swiper-slide" target="_blank">
              <img class="swiper-lazy" data-src="{{ asset($image->path) }}?w=385&h230&fit=crop&fm=webp&q=85"
                   src="{{ asset('img/pr385x230.jpg') }}" alt="">
            </a>
          @endforeach
        </div>
        <div class="swiper-button swiper-button-next"></div>
        <div class="swiper-button swiper-button-prev"></div>
      @endif
    </div>
    @if($hotel->moderate || !$hotel->show)
      <p class="show-tel-wrapper">
        <a class="btn btn-blue btn-block" href="{{ route('moderator.object.edit', $hotel->id) }}">Перейти в ЛК</a>
      </p>
    @else
      @if ($hotel->phone)
        <p class="show-tel-wrapper">
          <button class="btn btn-blue btn-block js-show-tel-btn" type="button">Показать телефон</button>
          <a href="tel:{{ $hotel->phone }}"
             class="btn btn-blue btn-block tel-link js-tel-link">{{ $hotel->phone }}</a>
        </p>
      @endif
    @endif

    <div class="hotel-card-content">
      <a href="{{ route('hotels.show', $hotel) }}" class="hotel-card-title" target="_blank">{{ $hotel->name }}</a>
      @if(count($hotel->metros))
        <ul class="hotel-card-metro">
          @foreach ($hotel->metros as $metro)
            @break($loop->index == 2)
            <li class="metro">
              <a href="/address/{{ Str::slug($hotel->address->city) }}/metro-{{ Str::slug($metro->name) }}">
                <i class="icon-metro mr-2" style="color: #{{ $metro->color }}"></i>
                <span class="name-metro">
                  <span>
                    {{ $metro->name }}
                  </span>
                </span>
                <span class="time-metro">
                  - {{ $metro->distance }} мин
                  <img class="svg-walk" src="{{asset('img/walk.svg')}}" alt="">
                </span>
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <p class="hotel-card-address">
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
              $url .= '/area-'.Str::slug($hotel->address->city_area)
            @endphp
            <a href="{{ $url }}">{{ $hotel->address->city_area }}</a>,
          @endif
          @if(isset($hotel->address->city_district) && !is_null($hotel->address->city_district))
            @php
              $has_district = true;
              $url .= '/district-'.Str::slug($hotel->address->city_district)
            @endphp
            <a href="{{ $url }}">{{ $hotel->address->city_district }}</a>,
          @endif
          @if(isset($hotel->address->street) && !is_null($hotel->address->street))
            {{ $hotel->address->street_with_type ?? $hotel->address->street }}@isset($hotel->address->house)
              , д.{{ $hotel->address->house }} @endisset
            @isset($hotel->address->block), к.{{ $hotel->address->block }} @endisset
          @endif
        </p>
      @endif
      <ul class="hotel-card-prices">
        @foreach($hotel->minimals AS $minimal)
          <li class="hotel-card-prices-item">
            @if($minimal->value === 0)
                  <p class="hotel-card-prices-item-title">{{ $minimal->name ?? '' }}</p>
                <p class="hotel-card-prices-item-info">{{ $minimal->info ?? '' }}</p> </p>
            @else
              <p class="hotel-card-prices-item-title">{{ $minimal->name ?? '' }}
                - от {{ $minimal->value ?? '' }}
                руб.</p>
              <p class="hotel-card-prices-item-info">{{ $minimal->info ?? '' }}</p>
            @endif
          </li>
          @break($loop->iteration > 2)
        @endforeach
      </ul>
    </div>

  </div>
</div>
