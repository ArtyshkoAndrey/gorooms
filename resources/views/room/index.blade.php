@extends('layouts.app')

@section('content')
    @include('web.parts.sections._search')
    <div class="breadcrumbs">
        <div class="container">
            <ul itemscope itemtype="https://schema.org/BreadcrumbList">
                <li  itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="/"> <span itemprop="name">Главная</span></a>
                    <meta itemprop="position" content="1"/>
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name">  {{ $title ?? 'Биржа номеров' }}</span>
                <meta itemprop="position" content="2"/>
                </li>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="container">
            <div class="section-header">
                <h1 class="section-title">
                    @if (Request::is('rooms/hot'))
                        {!! html_entity_decode(Settings::header('seo_/rooms/hot')) !!}
                    @else
                        {!! html_entity_decode(Settings::header('seo_/rooms')) !!}
                    @endif
                </h1>
                
                <form id="search-filter" class="search-filter" method="get">
                    <div class="search-filter-item search-filter-sort">
                            <button class="search-filter-price-btn {{ $sortByRequested['cost'] == 'desc' ? 'active' : '' }}" name="cost" value="desc"></button>
                            <button class="search-filter-price-btn {{ $sortByRequested['cost'] == 'asc' ? 'active' : '' }}" name="cost" value="asc"></button>
                            <label class="search-filter-label">Сортировать по цене</label>
                        </div>
                    <div class="search-filter-item">
                        <input type="checkbox" id="search-filter-popular" class="checkbox"
                               name="search-filter-popular">
                        <label for="search-filter-popular"
                               class="search-filter-label checkbox-label checkbox-label-dark">По размеру скидки</label>
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
                </form>        
            </div>
            <div class="items-container">
                @foreach($rooms AS $room)
                    @include('room._hot')
                @endforeach
            </div>
            <div class="show-more">
                <p class="show-more-counter">Загружено: {{ $rooms->count() }} ({{ $rooms->total()}})</p>
                @if($rooms->total() > $rooms->count())
                    <button id="rooms-load-more" class="show-more-btn" type="button">Загрузить еще</button>
                @endif
            </div>
        </div>
    </div>
    <section class="about">
        <div class="container">
            <article class="about-content">
                <div class="text-section about-text">
                    @if (Request::is('rooms/hot'))
                        {!! html_entity_decode(Settings::option('seo_/rooms/hot')) !!}
                    @else
                        {!! html_entity_decode(Settings::option('seo_/rooms')) !!}
                    @endif
                </div>
            </article>
        </div>
    </section>
@endsection