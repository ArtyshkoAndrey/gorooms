<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v={{ time() }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @if(config('app.debug'))
        <script src="{{ asset('js/vue-dev.js') }}"></script>
    @else
        <script src="{{ asset('js/vue@2.js') }}"></script>
    @endif
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                @auth
                <ul class="navbar-nav mr-auto d-flex align-items-center">
                    <li class="nav-item">
                        <a href="{{ route('admin.hotels.index') }}" class="nav-link">Список отелей</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Атрибуты <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('admin.attributes_categories.index') }}">
                                Категории атрибутов
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.attributes.index', 'room') }}">
                                Номера
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.attributes.index', 'hotel') }}">
                                Отели
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.moderators.index') }}" class="nav-link">Модераторы</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.cost_types.index') }}" class="nav-link">Типы цен</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.hotel_types.index') }}" class="nav-link">Типы размещений</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.ratings.index') }}" class="nav-link">Категории рейтинга</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.articles.index') }}" class="nav-link">Статьи</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.pages.index') }}" class="nav-link">Страницы</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.descriptions.index') }}" class="nav-link">Описания</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.instructions.index') }}" class="nav-link">Инструкции</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link">Настройки</a>
                    </li>
                </ul>
            @endauth
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <section class="errors">
        @if ($errors->any())
            <div class="container">
                <div class="row">
                    @foreach ($errors->all() as $error)
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>{{ $error }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
    <main class="py-4">
        @yield('content')
    </main>
</div>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
      integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.5/jquery.inputmask.min.js" integrity="sha512-sR3EKGp4SG8zs7B0MEUxDeq8rw9wsuGVYNfbbO/GLCJ59LBE4baEfQBVsP2Y/h2n8M19YV1mujFANO1yA3ko7Q==" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/css/suggestions.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/js/jquery.suggestions.min.js"></script>
<script src="https://cdn.tiny.cloud/1/ghdxavsus8orb1hk0kqxhd7ncwttt1anujdh4p7lmje0oiak/tinymce/5/tinymce.min.js"
        referrerpolicy="origin"></script>
<script src="https://cdn.tiny.cloud/1/z826n1n5ayf774zeqdphsta5v2rflavdm2kvy7xtmczyokv3/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    $("#address").suggestions({
        token: "a35c9ab8625a02df0c3cab85b0bc2e9c0ea27ba4",
        type: "ADDRESS",
    });
    $('.phone').inputmask("+7 (999) 999-99-99");
    tinymce.init({
        selector: 'textarea.editor',
        images_upload_url: '/admin/upload',
        automatic_uploads: true,
        block_unsupported_drop: false,
        plugins: "lists table image imagetools code",
        table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | fontsizeselect | code',
        height: 400,
        extended_valid_elements : "script[*]"
    });
    $(function() {
        // Multiple images preview in browser
        var imagesPreview = function(input, placeToInsertImagePreview) {

            if (input.files) {
                var filesAmount = input.files.length;

                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        $($.parseHTML('<div class="col-12 col-md-4 mb-3"><img class="img-fluid img-thumbnail" src="'+event.target.result+'"></div>')).appendTo(placeToInsertImagePreview);
                    }

                    reader.readAsDataURL(input.files[i]);
                }
            }

        };

        $('.js-image-preview').on('change', function() {
            imagesPreview(this, 'div.images');
        });
    });

</script>
@yield('js')
</body>
</html>
