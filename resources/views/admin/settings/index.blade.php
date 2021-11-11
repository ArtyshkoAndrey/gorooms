@extends('layouts.admin')

@php($first_col = 'col-2')
@php($second_col = 'col-8')
@php($third_col = 'col-2')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="h4 p-0 m-0">Настройки</div>
                </div>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.clear-cache') }}" class="btn btn-info">Сбросить кеш</a>
                <div class="row">
                    <div class="{{ $first_col }}">
                        <div class="h5">Опция</div>
                    </div>
                    <div class="{{ $second_col }}">
                        <div class="h5">Значение</div>
                    </div>
                    <div class="{{ $third_col }}">
                        <div class="h5">Действие</div>
                    </div>
                    <div class="col-12">
                        <div class="dropdown-divider"></div>
                    </div>
                </div>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Телефон</div>
                    <div class="{{ $second_col }}"><input type="text" class="form-control phone" name="phone"
                                                          value="{{ Settings::option('phone') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Email</div>
                    <div class="{{ $second_col }}"><input type="email" class="form-control" name="email"
                                                          value="{{ Settings::option('email') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Email для сообщений</div>
                    <div class="{{ $second_col }}"><input type="email" class="form-control" name="notify"
                                                          value="{{ Settings::option('notify') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Адрес</div>
                    <div class="{{ $second_col }}"><input type="text" class="form-control" name="address"
                                                          value="{{ Settings::option('address') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Facebook</div>
                    <div class="{{ $second_col }}"><input type="text" class="form-control" name="fb"
                                                          value="{{ Settings::option('fb') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Instagram</div>
                    <div class="{{ $second_col }}"><input type="text" class="form-control" name="instagram"
                                                          value="{{ Settings::option('instagram') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">ВКонтакте</div>
                    <div class="{{ $second_col }}"><input type="text" class="form-control" name="vk"
                                                          value="{{ Settings::option('vk') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">YouTube</div>
                    <div class="{{ $second_col }}"><input type="text" class="form-control" name="youtube"
                                                          value="{{ Settings::option('youtube') }}"></div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                @foreach( $pages as $page)
                    <form class="row" action="{{ route('admin.settings.seo.update', $page) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-8">

                            <div class="form-group">
                                <h3>{{ $names[$page->option] }}</h3>
                                <label for="content">H1</label>
                                <input type="text" class="form-control" name="header"
                                       value="{{ $page->header }}">
                                <label for="content">Seo описание</label>
                                <textarea name="value" id="content" class="form-control editor ">{{ old('content') ?? @$page->value ?? '' }}</textarea>
                                @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button class="btn btn-success">Обновить</button>
                        </div>
                    </form>
                @endforeach
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Город по умолчанию</div>
                    <div class="{{ $second_col }}">
                        <select name="city_default" id="city_default" class="form-control">
                            <option value="">Не выбрано</option>
                            @foreach($search_city AS $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.robot_store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Robots</div>
                    <div class="{{ $second_col }}">
                        <textarea class="form-control"
                                  style="min-height: 150px"
                                  name="content">{{ file_exists(public_path('robots.txt')) ? file_get_contents(public_path('robots.txt')) : '' }}</textarea>
                    </div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="dropdown-divider"></div>
                        <div class="h3 text-center">Цены</div>
                    </div>
                </div>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row mb-3">
                    @csrf
                    <div class="{{ $first_col }}">Соотношения</div>
                    <div class="{{ $second_col }}">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-4">
                                <label for="hour_cost_name">
                                    На час
                                </label>
                                <select name="hour_cost_name" id="hour_cost_name" class="form-control">
                                    @foreach(\App\Models\CostType::all() AS $cost_type)
                                        <option value="{{ $cost_type->id }}"
                                                @if(Settings::option('hour_cost_name') == $cost_type->id) selected @endif >{{ $cost_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label for="night_cost_name">
                                    На ночь
                                </label>
                                <select name="night_cost_name" id="night_cost_name" class="form-control">
                                    @foreach(\App\Models\CostType::all() AS $cost_type)
                                        <option value="{{ $cost_type->id }}"
                                                @if(Settings::option('night_cost_name') == $cost_type->id) selected @endif >{{ $cost_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label for="day_cost_name">
                                    На сутки
                                </label>
                                <select name="day_cost_name" id="day_cost_name" class="form-control">
                                    @foreach(\App\Models\CostType::all() AS $cost_type)
                                        <option value="{{ $cost_type->id }}"
                                                @if(Settings::option('day_cost_name') == $cost_type->id) selected @endif >{{ $cost_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="{{ $third_col }}">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.store') }}" method="POST" class="row">
                    @csrf
                    @foreach (['hour' => 'На час', 'night' => 'На ночь', 'day' => 'Сутки'] as $type => $title)
                        <div class="{{ $first_col }} mb-3">{{ $title }}</div>
                        <div class="{{ $second_col }} mb-3">
                            <div class="form-row">
                                <div class="col-12 col-md-4">
                                    <input type="number" class="form-control" name="{{ $type }}_cost_small"
                                           value="{{ Settings::option($type.'_cost_small') }}"
                                           placeholder="Цена минимум">
                                </div>
                                {{-- <div class="col-12 col-md-4">
                                    <input type="number" class="form-control" name="{{ $type }}_cost_medium"
                                           value="{{ Settings::option($type.'_cost_medium') }}"
                                           placeholder="Цена средне">
                                </div> --}}
                                <div class="col-12 col-md-4">
                                    <input type="number" class="form-control" name="{{ $type }}_cost_low"
                                           value="{{ Settings::option($type.'_cost_low') }}"
                                           placeholder="Цена максимум">
                                </div>
                            </div>
                        </div>
                        <div class="{{ $third_col }} mb-3">
                            @if($loop->index == 0)
                                <button class="btn btn-success">Обновить</button>
                            @endif
                        </div>

                    @endforeach
                </form>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="dropdown-divider"></div>
                        <div class="h3 text-center">Реестр времени</div>
                    </div>
                </div>
                <form action="{{ route('admin.periods.update.json') }}" id="vue-datetime-register" method="post" @submit="submit">
                    @csrf
                    <input type="text" name="result_json" v-model="out.value" v-show="out.show">
                    <h3>На час</h3>
                    <input class="form-control inline time-form" type="number" v-model="data.type_1.start_at" max="24" min="0" placeholder="3">
                    <input class="btn btn-info" type="button" value="Добавить" @click="add('1')">
                    <br><br>
                    <ul>
                        <li v-for="(el, i) in type_1">
                            <input class="form-control inline time-form" type="number" v-model="el.start_at" max="24" min="0" placeholder="0">
                            <svg @click="del('1', i)" width="20px" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </li>
                    </ul>
                    <h3>На ночь</h3>
                    <input class="form-control inline time-form" type="time" v-model="data.type_2.start_at">
                    -
                    <input class="form-control inline time-form" type="time" v-model="data.type_2.end_at">
                    <input class="btn btn-info" type="button" value="Добавить" @click="add('2')">
                    <br><br>
                    <ul>
                        <li v-for="(el, i) in type_2">
                            <input class="form-control inline time-form" type="time" v-model="el.start_at" >
                            -
                            <input class="form-control inline time-form" type="time" v-model="el.end_at" max="24">
                            <svg @click="del('2', i)" width="20px" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </li>
                    </ul>
                    <h3>На сутки</h3>
                    <input class="form-control inline time-form" type="time" v-model="data.type_3.start_at" >
                    -
                    <input class="form-control inline time-form" type="time" v-model="data.type_3.end_at" >
                    <input class="btn btn-info" type="button" value="Добавить" @click="add('3')">
                    <br><br>
                    <ul>
                        <li v-for="(el, i) in type_3">
                            <input class="form-control inline time-form" type="time" v-model="el.start_at" >
                            -
                            <input class="form-control inline time-form" type="time" v-model="el.end_at" max="24">
                            <svg @click="del('3', i)" width="20px" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </li>
                    </ul>
                    <input type="submit" class="btn btn-success" value="Обновить">
                </form>
                <script>
                  const DatatimeRegister = new Vue({
                    el: '#vue-datetime-register',
                    data() {
                      return {
                        startJson: '',
                        type_1: [],
                        type_2: [],
                        type_3: [],
                        out: {
                          show: false,
                          value: '',
                        },
                        data: {
                          type_1: {
                            start_at: '',
                          },
                          type_2: {
                            start_at: '',
                            end_at: '',
                          },
                          type_3: {
                            start_at: '',
                            end_at: '',
                          }
                        },
                        for_delete: [],
                      }
                    },
                    mounted() {
                      this.startJson = JSON.parse('{!! json_encode($periods) !!}');
                      this.type_1 = this.startJson.filter(el => el.cost_type_id === 1);
                      this.type_2 = this.startJson.filter(el => el.cost_type_id === 2);
                      this.type_3 = this.startJson.filter(el => el.cost_type_id === 3);
                    },
                    methods: {
                      submit() {
                        let obj = {
                          for_delete: this.for_delete,
                          data: [].concat(this.type_1, this.type_2, this.type_3),
                        };
                        this.out.value = JSON.stringify(obj);
                        this.out.show = true;
                      },
                      add(type_number) {
                        if (this.data['type_'+type_number].start_at === '') return;

                        let obj = {
                          cost_type_id: type_number,
                          start_at: this.data['type_'+type_number].start_at,
                          end_at: this.data['type_'+type_number].end_at ?? null,
                        }
                        this['type_'+type_number].push(obj)
                      },
                      del(type_number, i) {
                        if (this['type_'+type_number][i].id) {
                          this.for_delete.push(this['type_'+type_number][i].id);
                        }
                        this['type_'+type_number].splice(i,1);
                        console.log('На удалении: ', this.for_delete)
                      }
                    }
                  })
                </script>
                <style>
                    .inline {
                        display: inline-block !important;
                    }
                    .time-form {
                        width: 100px;
                    }
                </style>
            </div>
        </div>
    </div>
@stop
