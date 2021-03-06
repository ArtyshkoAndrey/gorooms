@extends('layouts.app')

@section('content')
  @widget('Filter')
  <section class="section">
    <div class="container">
      <div class="section-header">
        <h1 class="section-title">{!! $pageDescription->h1 ?? $pageDescription->title ?? $title !!}</h1>
      </div>
    </div>
  </section>

  <section class="section section-pt-none">
    @if($moderate ?? false)
      <div class="container items-container">
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
        <div class="row row-sm items-container">
          @foreach ($hotels as $hotel)
            @include('hotel._popular')
          @endforeach
        </div>
      </div>
    @endif

    @if ($hotels)
      <div class="show-more">
        <p class="show-more-counter">Загружено: {{ $hotels->count() }} ({{ $hotels->total() }})</p>
        @if($hotels->total() > $hotels->count())
          <button id="rooms-address-load-more" class="show-more-btn" type="button">Загрузить еще</button>
        @endif
      </div>
    @else
      <div class="show-more">
        <p class="show-more-counter">Загружено: {{ $rooms->count() }} ({{ $rooms->total()}})</p>
        @if($rooms->total() > $rooms->count())
          <button id="rooms-address-load-more" class="show-more-btn" type="button">Загрузить еще</button>
        @endif
      </div>
    @endif


    @if(!is_null($pageDescription))
      @if(isset($pageDescription->description))
        <div class="container" style="margin-top: 20px;">
          {!! html_entity_decode($pageDescription->description) !!}
        </div>
      @endif
    @endif
  </section>
@endsection

@section('scripts')
  <script>
    $(function () {
      let roomPageCount = {{ Request::get('page', 1) + 1 }};
      $('#rooms-address-load-more').click(async function (e) {

        try {
          @if(Request::routeIs('search.address'))
            await loadMore(e, `{{URL::full()}}?page=${roomPageCount}&api=1`, 16);
          @else
            await loadMore(e, "{!! Request::fullUrl() !!}" + "&page=" + roomPageCount + "&api=1", 16);
          @endif

          roomPageCount++
        } catch (e) {
          console.log('Error Vasya', e)
        }
      });
    });
  </script>
@endsection

