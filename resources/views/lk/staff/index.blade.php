@extends('lk.layouts.app')

@section('content')

  @if ($errors->any())
    <div class="alert alert-danger position-absolute" style="width: 320px; top: 100px; right: 20px">
      <ul class="list-unstyled mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section class="part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title title_black">Сотрудники</h2>
        </div>
      </div>
      <div class="row justify-content-center">

        {{-- General users --}}
        @foreach($users as $user)
          @if ($user->pivot->hotel_position === App\User::POSITION_GENERAL)
            <div class="col-4 mt-2">
              <div class="staff-item staff-item_general" data-id="{{ $user->id }}">
                <div class="staff-item__name staff-item__name_blue">{{ App\User::POSITIONS_LANGUAGE[$user->pivot->hotel_position] }}</div>
                <div class="staff-item__bottom">
                  <p class="staff-item__text">{{ $user->position }}</p>
                  <p class="staff-item__text">{{ $user->phone }}</p>
                  <p class="staff-item__text text-truncate w-100 px-1">{{ $user->name }}</p>
                </div>
              </div>
            </div>
          @endif
        @endforeach

        {{-- Create general users --}}
        @for($i = 3 - $userGeneralCount; $i > 0; $i--)
          <div class="col-4 mt-2">
            <div class="staff-item staff-item_general"
                 data-id="createUser"
                 data-position="{{ App\User::POSITION_GENERAL }}"
                 data-position-language="{{ App\User::POSITIONS_LANGUAGE[App\User::POSITION_GENERAL] }}">
              <div class="staff-item__name">General</div>
              <div class="staff-item__bottom">
                <p class="staff-item__text">Директор</p>
                <p class="staff-item__text">+7 ( _ _ _ ) _ _ _ _ _ _</p>
                <p class="staff-item__text text-truncate w-100 px-4">Иван Иванов Иванович</p>
              </div>
            </div>
          </div>
        @endfor

        {{-- Staff users --}}
        @foreach($users as $user)
          @if ($user->pivot->hotel_position === App\User::POSITION_STAFF)
            <div class="col-4 mt-2">
              <div class="staff-item staff-item_small staff-item_staff" data-id="{{ $user->id }}">
                <div class="staff-item__name staff-item__name_orange">{{ App\User::POSITIONS_LANGUAGE[$user->pivot->hotel_position] }}</div>
                <div class="staff-item__bottom">
                  <p class="staff-item__text">{{ $user->position }}</p>
                  <p class="staff-item__text">{{ $user->phone }}</p>
                  <p class="staff-item__text text-truncate w-100 px-4">{{ $user->name }}</p>
                </div>
              </div>
            </div>
          @endif
        @endforeach

        {{-- Create Staff users --}}
        @for($i = 5 - $userStaffCount; $i > 0; $i--)
          <div class="col-4 mt-2">
            <div class="staff-item staff-item_small staff-item_staff"
                 data-id="createUser"
                 data-position="{{ App\User::POSITION_STAFF }}"
                 data-position-language="{{ App\User::POSITIONS_LANGUAGE[App\User::POSITION_STAFF] }}">
              <div class="staff-item__name">Staff</div>
              <div class="staff-item__bottom">
                <p class="staff-item__text">Должность</p>
                <p class="staff-item__text">+7 ( _ _ _ ) _ _ _ _ _ _</p>
                <p class="staff-item__text">Полное имя сотрудника</p>
              </div>
            </div>
          </div>
        @endfor

      </div>
    </div>

  </section>

  <div class="overlay">
  </div>

  @foreach($users as $user)
    <div class="popup" data-id="{{ $user->id }}">
      <img src="{{ asset('img/lk/close.png') }}" alt="" class="close-this">
      <h2 class="title title_blue popup__title">{{ App\User::POSITIONS_LANGUAGE[$user->pivot->hotel_position] }}</h2>
      <form action="{{ route('lk.staff.update', $user->id) }}" method="post">
        @csrf
        @method('PUT')
        <input type="text"
               class="field"
               placeholder="ФИО"
               name="name"
               value="{{ $user->name }}"
               required
        >
        <input type="phone"
               class="field"
               placeholder="+7 ( _ _ _ ) _ _ _  _ _  _ _"
               name="phone"
               value="{{ $user->phone }}"
               required
        >
        <input type="email"
               class="field"
               placeholder="E-mail"
               value="{{ $user->email }}"
               name="email"
               required
        >
        <input type="text"
               class="field"
               placeholder="Должность"
               value="{{ $user->position }}"
               name="position"
               required
        >
        <input type="password"
               class="field"
               placeholder="Придумайте пароль"
               name="password"
        >
        <input type="text"
               class="field"
               placeholder="Придумайте кодовое слово"
               value="{{ $user->code }}"
               name="code"
               required
        >
        <div class="d-flex align-items-center popup-buttons">
          <div class="d-flex align-items-center">
            <button type="submit" class="button button_blue">Сохранить</button>
            <button type="button"
                    onclick="event.preventDefault(); $('.popup[data-id={{ $user->id }}] form.generate-password-user').submit()"
                    class="button button_gray reset"
            >
              Сбросить пароль
            </button>
          </div>
          @if(auth()->id() !== $user->id)
            <button class="staff-remove"
                    type="button"
                    onclick="event.preventDefault(); $('.popup[data-id={{ $user->id }}] form.remove-user').submit()">
              <img src="{{ asset('img/lk/bin.png') }}" alt="">
            </button>
          @endif
        </div>
      </form>

      <form action="{{ route('lk.staff.remove', $user->id) }}" method="POST" class="remove-user d-none">
        @method('DELETE')
        @csrf
      </form>

      <form action="{{ route('lk.staff.update.password', $user->id) }}" method="post" class="generate-password-user d-none">
        @csrf
      </form>
    </div>
  @endforeach

{{-- Create users in hotels model with props --}}
  @if($userStaffCount < 5 || $userGeneralCount < 3)
    <div class="popup" data-id="createUser">
    <img src="{{ asset('img/lk/close.png') }}" alt="" class="close-this">
    <h2 class="title title_blue popup__title">LOL</h2>
    <form action="{{ route('lk.staff.create') }}" method="POST">
      @csrf
      <input type="hidden" name="hotel_position">
      <input type="text"
             class="field"
             placeholder="ФИО"
             name="name"
             required
      >
      <input type="phone"
             class="field"
             placeholder="+7 ( _ _ _ ) _ _ _  _ _  _ _"
             name="phone"
             required
      >
      <input type="email"
             class="field"
             placeholder="E-mail"
             name="email"
             required
      >
      <input type="text"
             class="field"
             placeholder="Должность"
             name="position"
             required
      >
      <input type="password"
             class="field"
             name="password"
             placeholder="Придумайте пароль"
      >
      <input type="text"
             class="field"
             placeholder="Придумайте кодовое слово"
             name="code"
             required>
      <div class="d-flex align-items-center popup-buttons">
        <div class="d-flex align-items-center">
          <button type="submit" class="button button_blue">Сохранить</button>
        </div>
      </div>
    </form>
  </div>
  @endif
@endsection


@section('js')
  <script>
      $("input[type='phone']").mask("+7 (999) 999 99-99");
  </script>
@endsection