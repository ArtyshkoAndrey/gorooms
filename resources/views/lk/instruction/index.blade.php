@extends('lk.layouts.app')

@section('content')

  <section class="part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_blue">
            Инструкции
          </h2>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <ul class="gyperlinks">
            @foreach($instructions as $i)
              <li class="gyperlink">
                <a class="d-flex align-items-center" href="#partText{{$i->id}}">
                  <img src="{{ asset('img/lk/star.png') }}" alt="">
                  <p class="text-bold">{{ $i->header }}</p>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </section>

  @foreach($instructions as $i)
    <section class="part part_text" id="partText{{ $i->id }}">
      <div class="container">
        <div class="row part__top">
          <div class="col-12">
            <h2 class="title title_blue">{{ $i->header }}</h2>
          </div>
        </div>
        <div class="row part__middle align-items-center tiny-content">
          {!! $i->content !!}
        </div>
      </div>
    </section>
  @endforeach
@endsection

@section('js')
  <script>
    $(document).ready(function () {
      setTimeout(() => {
        $('iframe').each(function (frame) {
          console.log(this)
          $($(this).parent()).addClass('w-100 mb-3')
        })
      }, 500)
    })
  </script>
@endsection