<?php

namespace App\Http\Controllers;

use Exception;
use App\Settings;
use App\Helpers\{Json, Area};
use App\Models\{Hotel, Room, Metro, Address};
use App\Models\Search;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Middleware\SetCityCoords;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{

  public function __invoke(Request $request)
  {
    $query = $request->get('query', '');
    $attributes = $request->get('attributes', ['hotel' => [], 'room' => []]);

    $sortByRequested = [
      'sortByCost' => false,
    ];

    if ($request->has('sortByCost')) {
      $sortByRequested['sortByCost'] = $orderCost = $request->input('sortByCost');
    }

    $city = '';
    if (!$request->is('api/*'))
      $city = $request->session()->get('city', Settings::option('city_default', false));
    $address = $request->get('address', ['city' => $city, 'city_district' => '', 'region' => '']);
    $hotel_type = $request->get('hotel_type', false);
    $metro = $request->get('metro', false);
    $hot = $request->has('hot');
    $with_map = \Request::route()->getName() == 'search.map' || $request->is('/search_map/*');

    $search = Search::makeSearchBuilder();

    $is_room = (isset($attributes['room']) && count($attributes['room'])) || $hot;

    $rooms = false;
    $query_args = $search->createQueryArray($query);
    $moderate = $request->has('moderate');
    if ($moderate) {
      $hotels = Hotel::withoutGlobalScope('moderation')
        ->where('moderate', true)
        ->where('old_moderate', true)
        ->orWhereHas('rooms', function ($q) {
          $q->where('moderate', true);
        })
        ->orderBy('id')
        ->paginate(5);
      $hotels->appends(['moderate' => true]);
      $is_room = false;
//      $hotels = $hotels->get();
    } else {

      if (!$is_room) {
        if ($request->has('sortByCost')) {
          $orderCost = $request->input('sortByCost');

          $hotels = Hotel::orderByCost($orderCost)->with(['rooms', 'address', 'attrs']);
        } else {
          $hotels = Hotel::with(['rooms', 'address', 'attrs']);
        }

        if ($request->has('hotel_moderate')) {
          $hotels->where('moderate', 1)->orWhere('show', 0);
        }
        foreach ($query_args as $arg) {
          $hotels->where('name', 'LIKE', $arg)->orWhereHas('address', function (Builder $builder) use ($arg) {
            $builder->where('value', 'LIKE', $arg);
          });
        }

        foreach ($address as $key => $value) {
          if (empty($value))
            continue;
          $hotels->whereHas('address', function (Builder $builder) use ($key, $value) {
            $builder->where($key, $value);
          });
        }
        if ($hotel_type)
          $hotels->where('type_id', $hotel_type);
        if ($metro)
          $hotels->whereHas('metros', function (Builder $builder) use ($metro) {
            $builder->whereRaw('LOWER(name) = LOWER(?)', [$metro]);
          });

        $hotels = $hotels->get();
        if (count($attributes['hotel'])) {
          $hotels = $hotels->map(function ($hotel) use ($attributes) {
            $diffs = array_intersect($hotel->attrs()->pluck('id')->toArray(), $attributes['hotel']);
            if (count($diffs) === count($attributes['hotel']))
              return $hotel;
            return null;
          })->whereNotNull();
        }
        if ($cost = $this->getCost($request)) {
          $before = $hotels->count();
          $costs = [];
          $hotels = $hotels->filter(function (Hotel $hotel, int $index) use ($cost, &$costs) {
            $costs = $hotel->getMinCosts();
            foreach ($costs as $item) {

              if (isset($item->id)) {
                if ($item->id !== $cost['type']) {
                  continue;
                }
                if ($cost['condition'] === 'BETWEEN') {
                  return $item->value >= $cost['value'][0] && $item->value <= $cost['value'][1];
                }

                if ($cost['condition'] === '>') {
                  return $item->value > $cost['value'][0];
                }

                if ($cost['condition'] === '>=') {
                  return $item->value >= $cost['value'][0];
                }

                if ($cost['condition'] === '<') {
                  return $item->value < $cost['value'][0];
                }

                if ($cost['condition'] === '<=') {
                  return $item->value <= $cost['value'][0];
                }
              }

              return false;
            }
          })->all();
          $hotels = collect($hotels);
        }
      }
      else {
        if ($request->has('sortByCost')) {
          $orderCost = $request->input('sortByCost');

          $rooms = Room::orderByCost($orderCost)->with(['hotel', 'hotel.address']);
        } else {
          $rooms = Room::with(['hotel', 'hotel.address']);
        }
        
        if ($request->has('room_moderate')) {
          $rooms->where('moderate', 1);
        }
        
        foreach ($query_args as $arg) {
          $rooms->where('name', 'LIKE', $arg)->orWhereHas('hotel.address', function (Builder $builder) use ($arg) {
            $builder->where('value', 'LIKE', $arg);
          });
        }

        foreach ($address as $key => $value) {
          if (empty($value) || is_null($value))
            continue;
          $rooms->whereHas('hotel.address', function (Builder $builder) use ($key, $value) {
            $builder->where($key, '=', $value);
          });
        }

        if ($hotel_type)
          $rooms->whereHas('hotel', function (Builder $builder) use ($hotel_type) {
            $builder->where('type_id', $hotel_type);
          });
        if ($metro)
          $rooms->whereHas('hotel.metros', function (Builder $builder) use ($metro) {
            $builder->whereRaw('LOWER(name) = LOWER(?)', [$metro]);
          });
        if ($cost = $this->getCost($request)) {
          $rooms->whereHas('costs', function (Builder $builder) use ($cost) {
            $builder->whereHas('period', function (Builder  $b) use ($cost) {
              $b->where('cost_type_id', $cost['type']);
            })->where('value', '>', 0);
            if ($cost['condition'] === 'BETWEEN') {
              $builder->whereBetween('value', $cost['value']);
            } else {
              $builder->where('value', $cost['condition'], $cost['value']);
            }
          });
        }
        $rooms = $rooms->get();
        if (count($attributes['room'])) {
          $rooms = $rooms->map(function ($room) use ($attributes) {
            $diffs = array_intersect($room->attrs()->pluck('id')->toArray(), $attributes['room']);
            if (count($diffs) === count($attributes['room']))
              return $room;
            return null;
          })->whereNotNull();
        }
        if (isset($attributes['hotel']) && count($attributes['hotel'])) {
          $rooms = $rooms->map(function ($room) use ($attributes) {
            $diffs = array_intersect($room->hotel->attrs()->pluck('id')->toArray(), $attributes['hotel']);
            if (count($diffs) === count($attributes['hotel']))
              return $room;
            return null;
          })->whereNotNull();
        }
        if ($hot)
          $rooms->where('is_hot', '=', true);
        $hotels = Hotel::whereIn('id', $rooms->pluck('hotel.id')->toArray());

        $hotels = $hotels->get();
      }

    }
    if ($hotel_id = $request->get('hotel', false)) {
      $rooms = $is_room ? $rooms->where('hotel_id', $hotel_id) : $hotels->where('id', $hotel_id)->first()->rooms;
    }

    if ($request->is('render/*')) {
      $page = $request->get('page', 1);
      if ($is_room) {
        $per_page = Room::PER_PAGE;
        $rooms->forPage($page, $per_page);
        return view('render.room.index', compact('rooms'));
      }
      $per_page = Hotel::PER_PAGE;
      $hotels = $hotels->forPage($page, $per_page);
      return view('render.hotel.index', compact('hotels'));
    }
    if ($moderate) {
      $count = Hotel::withoutGlobalScope('moderation')
        ->where('moderate', true)
        ->where('old_moderate', true)
        ->count();
    } else {
      $count = $is_room ? $rooms->count() : $hotels->count();
    }
    $title = $with_map ? 'Поиск по карте' : "Результаты поиска <span class=\"count\">($count)</span>";

    try {
      if ($address['city'] && !$request->is('api/*')) {
        @$request->session()->put('city', $address['city']);
        @SetCityCoords::set($request);
      }
    } catch (Exception $exception) {
      if ($request->is('api/*')) {
        return Json::bad(['error' => $exception->getMessage()]);
      }
    }

    if ($request->is('api/*')) {
      return Json::good(['count' => $count]);
    }

    /* START SEO */


    //pageAbout
    $pageDescription = new class {
    };

    // Удобства в виде человекочитаемого списка
    $attr = $request->get('attributes');
    $attr = array_merge(@$attr['hotel'] ?: [], @$attr['room'] ?: []);
    $attr = Attribute::whereIn('id', $attr)->pluck('name')->toArray();
    $pageDescription->h1 = ", " . implode(", ", $attr);
    foreach ($attr as &$a) {
      // $a = "\"$a\"";


    }
    $attr = empty($attr) ? null : implode(', ', $attr);

    // Переменная для передачи в представление
    if ($queryAddress = $request->get('address')) {
      if (@$queryAddress['city']) {
        $slugs = [
          'city' => @$queryAddress['city'],
          'area' => @$queryAddress['city_area'],
          'district' => @$queryAddress['city_district'],
          'street' => @$queryAddress['city_street'],
          'metro' => @$request->get('metro')
        ];

      }
    }

    if ((!empty($slugs['city'])) && (!empty($slugs['area'])) && (empty($slugs['district'])) && (empty($slugs['street'])) && (empty($slugs['metro']))) {

      if (!is_null($attr)) {
        $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" с выбранными условиями ' . $attr;
        $pageDescription->meta_description = 'Ищете отель? Бронируйте гостиничные номера на час (сутки) с ' . $attr . ' в "' . $slugs['area'] . '" Москвы ▶Описание номеров с фото  ▶ Звоните!';

      } else {
        $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '"';
        $pageDescription->meta_description = 'Ищете отель? Бронируйте гостиничные номера на час (сутки) с ' . $attr . ' в "' . $slugs['area'] . '" Москвы ▶Описание номеров с фото  ▶ Звоните!';

      }


    }

    if (!empty($slugs['street'])) {

      if (!empty($slugs['metro'])) {

        if ($attr) {

          // Улица + метро + страница фильтрации по удобству
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",около метро "' . $slugs['metro'] . '" на улице "' . $slugs['street'] . '" с выбранными условиями ' . $attr;
          //$pageDescription->title = 'Выгодные цены на отели с '.$attr.' на "'.$slugs['street'].'" рядом с метро "'.$slugs['metro'].'"';
          $pageDescription->meta_description = 'Выбирайте и бронируйте почасовую гостиницу с ' . $attr . ' на "' . $slugs['street'] . '" рядом с метро "' . $slugs['metro'] . '" ▶ Актуальные цены ▶Обслуживание 24/7  ▶ Звоните!';
        } else {

          // метро + улица
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",около метро "' . $slugs['metro'] . '" на улице "' . $slugs['street'] . '" ';
          // $pageDescription->title = 'Бронь отелей на "'.$slugs['street'].'" рядом с метро "'.$slugs['metro'].'" онлайн';
          $pageDescription->meta_description = 'Компания Gorooms предлагает отели рядом с метро "' . $slugs['metro'] . '" по улице "' . $slugs['street'] . '" в Москве ▶Описание номеров с фото ▶ Доступные цены ▶ Бронируйте уже сейчас!';
        }

      } else {

        if ($attr) {

          // Улица + страница фильтрации по удобству
          $pageDescription->title = 'Гостницы с ' . $attr . ' на "' . $slugs['street'] . '" улице - бронь онлайн';
          $pageDescription->meta_description = 'Ищете отель? Бронируйте гостиничные номера на час (сутки) с ' . $attr . ' на "' . $slugs['street'] . '" улице  Москвы ▶Свободные номера с актуальными ценами ▶ Звоните!';
        } else {

          // Улицы
          $pageDescription->title = 'Бронь отелей на "' . $slugs['street'] . '" улице Москвы недорого';
          $pageDescription->meta_description = 'Выбирайте и бронируйте гостиницу с номерами на час (сутки) на "' . $slugs['street'] . '" улице в Москве ▶Описание номеров с фото и контактами ▶ Доступные цены ▶ Звоните!';
        }

      }
    } else if (!empty($slugs['district'])) {

      if (!empty($slugs['metro'])) {

        if ($attr) {

          // Район + метро + страница фильтрации по удобству
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" в районе "' . $slugs['district'] . '" около метро  "' . $slugs['metro'] . '" с выбранными условиями ' . $attr . '';
          //  $pageDescription->title = 'Отели с '.$attr.' в "'.$slugs['district'].'" районе рядом с м. "'.$slugs['metro'].'" - бронь онлайн';
          $pageDescription->meta_description = 'Компания Gorooms предлагает отели с ' . $attr . ' в "' . $slugs['district'] . '" районе рядом с м. "' . $slugs['metro'] . '" Москвы ▶Просторные номера▶ Доступные цены ▶ Бронируйте уже сейчас!';
        } else {

          // Метро+район
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" в районе "' . $slugs['district'] . '" около метро  "' . $slugs['metro'] . '"';
          // $pageDescription->title = 'Отели у метро "'.$slugs['metro'].'" в "'.$slugs['district'].'" районе Москвы -бронь, цены';
          $pageDescription->meta_description = 'Ищете отель? Бронируйте гостиницу с почасовыми номерами рядом с метро "' . $slugs['metro'] . '" в "' . $slugs['district'] . '" районе Москвы ▶Описание номеров с фото  ▶ Звоните!';
        }

      } else {

        if ($attr) {

          // Район + страница фильтрации по удобству
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '", в районе "' . $slugs['district'] . '" с выбранными условиями ' . $attr . '';
          // $pageDescription->title = 'Отели в городе "'.$slugs['district'].'" с выбранными условиями '.$attr.'';
          //$pageDescription->title = '111Гостиницы с '.$attr.' в "'.$slugs['district'].'" - бронь онлайн';
          $pageDescription->meta_description = 'Компания Gorooms предлагает отели с "' . $attr . '" в "' . $slugs['district'] . '" районе Москвы ▶Просторные номера▶ Доступные цены ▶ Бронируйте уже сейчас!';
        } else {

          // Районы
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '", в районе "' . $slugs['district'] . '" ';
          // $pageDescription->title = 'Гостиницы в "'.$slugs['district'].'" районе  Москвы - цены, фото, бронирование';
          $pageDescription->meta_description = 'Ищете отель? С Gorooms Вы сможете быстро подобрать отель на час (сутки) в "' . $slugs['district'] . '" районе Москвы ▶ Подробное описание номеров с фото ▶ Бронируйте уже сейчас!';
        }

      }
    } else if (!empty($slugs['metro'])) {

      if (!empty($slugs['area'])) {

        if ($attr) {

          // Округ + метро + страница фильтрации по удобству
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" около метро  "' . $slugs['metro'] . '"с выбранными условиями ' . $attr;
          //  $pageDescription->title = 'Бронь отелей с '.$attr.' в "'.$slugs['area'].'" Москвы рядом с метро "'.$slugs['metro'].'" ';
          $pageDescription->meta_description = 'Забронируйте гостиницу на час с ' . $attr . ' в "' . $slugs['area'] . '" Москвы рядом с метро "' . $slugs['metro'] . '"▶ Свободные номера и актуальные цены ▶ Заказывайте уже сейчас!';
        } else {

          // метро + округ
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" около метро  "' . $slugs['metro'] . '"';
          //  $pageDescription->title = 'Отели рядом с метро "'.$slugs['metro'].'" в "'.$slugs['area'].'" Москвы - бронь онлайн';
          $pageDescription->meta_description = 'Ищете отель? Бронируйте гостиницу с почасовыми номерами рядом с метро "' . $slugs['metro'] . '" в "' . $slugs['area'] . '" районе Москвы ▶Описание номеров с фото  ▶ Звоните!';
        }

      } else {

        if ($attr) {

          // Метро + страница фильтрации по удобству
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" около метро  "' . $slugs['metro'] . '"с выбранными условиями ' . $attr;
          //$pageDescription->title = 'Отели с '.$attr.' рядом с "'.$slugs['metro'].'" метро - бронь, цены';
          $pageDescription->meta_description = 'Бронируйте уютные гостиничные номера с ' . $attr . ' рядом с метро "' . $slugs['metro'] . '" по приятным ценам в Москве ▶Описание номеров с фото  ▶ Звоните!';
        } else {

          // Станции метро
          $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" около метро  "' . $slugs['metro'] . '"';
          // $pageDescription->title = 'Отели у метро "'.$slugs['metro'].'"  - выгодные цены в Москве';
          $pageDescription->meta_description = 'Бронирование отелей рядом с метро "' . $slugs['metro'] . '" по минимальным ценам  ▶ Подробное описание номеров с фото и контактами ▶ Уютные номера с удобствами  ▶ Звоните!';
        }

      }

    } else if ($attr) {

      if (!empty($slugs['area'])) {
        // Округ + страница фильтрации по удобству
        $pageDescription->title = 'Отели в городе"' . $slugs['city'] . '",в "' . $slugs['area'] . '" с выбранными условиями ' . $attr;
        $pageDescription->meta_description = 'Ищете отель? Бронируйте гостиничные номера на час (сутки) с ' . $attr . ' в "' . $slugs['area'] . '" Москвы ▶Описание номеров с фото  ▶ Звоните!';
      } else {

        // Просто страница фильтрации по удобству (все варианты удобств)
        $pageDescription->title = 'Отели в Москве со всеми удобствами в номере - бронь номеров онлайн';
        $pageDescription->meta_description = 'Выбирайте и бронируйте комфортабельные гостиничные номера на час (сутки) со всеми удобствами в компании Gorooms ▶Выгодные цены ▶Качественный сервис ▶ Звоните!';
      }
    }


    $title = '';
    if (isset($slugs['city'])) {
      $title = ($is_room ? 'Номера города ' : 'Отели города ')
        . $slugs['city']
        . (isset($slugs['area']) ? ', ' . $slugs['area'] . ' округ ' : '')
        . (isset($slugs['district']) ? ', ' . $slugs['district'] . ' район ' : '')
        . (isset($slugs['street']) ? ', ул. ' . $slugs['street'] : '')
        . (isset($slugs['metro']) ? ', метро "' . $slugs['metro'] . '"' : '');

    }
    if (empty($title)) {
      $title = "Результаты поиска";
    }
    $title .= "<span class=\"count\">($count)</span>";

    if (is_null($hotel_type)) {
      $desc_city_name = 'Отели ';
    } else {
      switch ($hotel_type) {
        case 1:
          $desc_city_name = 'Отели ';
          break;
        case 2:
          $desc_city_name = 'Аппартаменты ';
          break;
        case 3:
          $desc_city_name = 'Гостевые дома ';
          break;
        case 4:
          $desc_city_name = 'Сауны ';
          break;
      }
    }


    if (!empty(@$queryAddress['city'])) {
      $desc_city = $desc_city_name . " в городе " . @$queryAddress['city'];
    } else {
      $desc_city = '';
    }
    if (!empty(@$queryAddress['city_area'])) {
      $desc_area = " в " . @$queryAddress['city_area'];
    } else {
      $desc_area = '';
    }
    if (!empty(@$queryAddress['city_district'])) {
      $desc_district = " в районе " . @$queryAddress['city_district'];
    } else {
      $desc_district = '';
    }
    if (!empty(@$queryAddress['city_street'])) {
      $desc_street = " на улице " . @$queryAddress['city_street'];
    } else {
      $desc_street = '';
    }
    // return dd(@$request->get('metro'));
    if (@$request->get('metro')) {
      $desc_metro = " около метро " . @$request->get('metro');
    } else {
      $desc_metro = '';
    }
    if ($attr) {
      $desc_attr = " с выбранными условиями " . $attr;
    } else {
      $desc_attr = '';
    }
    $pageDescription->title = $desc_city . $desc_area . $desc_district . $desc_street . $desc_metro . $desc_attr;

    if ($moderate) {
      $title = 'На модерации' . "<span class=\"count\">($count)</span>";
      $pageDescription->title = 'На модерации' . "<span class=\"count\">($count)</span>";
    }

    // 'city' => @$queryAddress['city'],
    // 'area' => @$queryAddress['city_area'],
    // 'district' => @$queryAddress['city_district'],
    // 'street' => @$queryAddress['city_street'],
    // 'metro' => @$request->get('metro')


    if (!@$pageDescription->title or !@$pageDescription->meta_description) {
      $pageDescription = null;
    }

    /* END SEO */

    return view('web.search', compact('hotels', 'moderate', 'query', 'rooms', 'with_map', 'title', 'attributes', 'address', 'request', 'pageDescription', 'sortByRequested'));
  }

  public function hint(Request $request)
  {
    $query = $request->get('query', '');

    if(!$query) {
      return response()->json([], 200);
    }

    $items = [];

    $search = Search::makeSearchBuilder();

    $query_args = $search->createQueryArray($query);

    foreach ($query_args as $arg) {
      // 5 - 3 letters + 2 special symbols - "%"
      if(strlen($arg) < 5) continue; 

      $arg = substr($arg, 1);

      $hotels = Hotel::where('name', 'LIKE', $arg)->orWhereHas('address', function (Builder $builder) use ($arg) {
        $builder->where('value', 'LIKE', $arg);
      })->get();

      foreach ($hotels as $hotel) {
        $items[] = $hotel->name;
      }

      $metros = Metro::where('name', 'LIKE', $arg)->get();

      foreach ($metros as $metro) {
        $items[] = $metro->name;
      }

      $rooms = Room::where('name', 'LIKE', $arg)->get();

      foreach ($rooms as $room) {
        $items[] = $room->name;
      }

      $addresses_regions = Address::where('region', 'LIKE', $arg)->get();

      foreach ($addresses_regions as $address) {
        $items[] = $address->region;
      }

      $addresses_cities = Address::where('city', 'LIKE', $arg)->get();

      foreach ($addresses_cities as $address) {
        $items[] = $address->city;
      }

      $addresses_streets = Address::where('street_with_type', 'LIKE', $arg)->get();

      foreach ($addresses_streets as $address) {
        $items[] = $address->street_with_type;
      }

      $addresses_districts = Address::where('city_district', 'LIKE', $arg)->get();

      foreach ($addresses_districts as $address) {
        $items[] = $address->city_district;
      }

      $addresses_areas = Address::where('city_area', 'LIKE', $arg)->get();

      foreach ($addresses_areas as $address) {
        $items[] = $address->city_area;
      }

    }

    $items = array_unique($items);

    return response()->json(compact('items'), 200);
  }

  private function getCost(Request $request)
  {

    $cost_raw = $request->get('cost', false);

    if (!$cost_raw)
      return false;
    $conditions = [
      'gt' => '>',
      'lt' => '<',
      'gte' => '>=',
      'lte' => '<=',
      'between' => 'BETWEEN'
    ];

    $types = [
      'hour' => Settings::option('hour_cost_name'),
      'night' => Settings::option('night_cost_name'),
      'day' => Settings::option('day_cost_name'),
      '23hour' => Settings::option('day_cost_name'),
    ];

    [$type, $condition, $value] = explode('.', $cost_raw);
    $value = explode('-', $value);
    $condition = $conditions[$condition];
    $type = $types[$type];
    return compact('type', 'value', 'condition');
  }

  public function address(Request $request, $city, $param1 = null, $param2 = null, $param3 = null, $param4 = null)
  {
    $slugs = [];

    $findParam = function ($param) use ($param1, $param2, $param3, $param4) {
      switch (true) {
        case substr($param1 ?: '', 0, strlen($param)) === $param:
          return $param1;
        case substr($param2 ?: '', 0, strlen($param)) === $param:
          return $param2;
        case substr($param3 ?: '', 0, strlen($param)) === $param:
          return $param3;
        case substr($param4 ?: '', 0, strlen($param)) === $param:
          return $param4;
      }
      return null;
    };

    $addresses = [
      'city' => 'city-' . $city,
      'area' => $findParam('area-'),
      'district' => $findParam('district-'),
      'street' => $findParam('street-'),
      'metro' => $findParam('metro-')
    ];

    foreach ($addresses as $type => $value) {
      if (empty($value)) continue;
      $data = explode('-', $value);
      $slug = $data[0];
      $content = implode('-', array_slice($data, 1));
      $slugs[$slug] = $content;
    }
    $hotels = Search::getBySlug($slugs);

    $query = '';

    $rooms = Room::whereIn('hotel_id', $hotels->pluck('id')->toArray())->paginate(20);
    
    $with_map = false;
    $title = 'Отели города ';
    if (isset($slugs['city'])) {
      $title .= $slugs['city']
        . (isset($slugs['area']) ? ', ' . $slugs['area'] . ' округ ' : '')
        . (isset($slugs['district']) ? ', ' . $slugs['district'] . ' район ' : '')
        . (isset($slugs['street']) ? ', ул. ' . $slugs['street'] : '')
        . (isset($slugs['metro']) ? ', метро "' . $slugs['metro'] . '"' : '');

    }

    
    $attributes = [
      'hotel' => [],
      'room' => [],
    ];

    $address = $slugs;

    if(isset($address['area']) && $address['area']) {
      $address['short_area'] = Area::short($address['area']);
    }

    return view(isset($request["page"]) ? 'web.search_' : 'web.search', compact('hotels', 'query', 'rooms', 'with_map', 'title', 'attributes', 'address', 'request'));
  }
}

