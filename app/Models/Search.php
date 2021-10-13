<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * App\Models\Search
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Search newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search query()
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Search extends Model
{

  /**
   * @var Builder $builder
   */
  private $builder;
  private $searchable = false;

  public static function makeSearchBuilder(): Search
  {

    $search = new Search;

    $builder = DB::table('hotels')
      ->leftJoin('addresses', 'hotels.id', '=', 'addresses.hotel_id')
      ->leftJoin('rooms', 'hotels.id', '=', 'rooms.hotel_id');

    return $search->setBuilder($builder);
  }

  public static function getBySlug(&$slugs)
  {
    $hotels = static::getBySlugQuery($slugs);

    return $hotels->get();
  }

  static function getBySlugQuery(&$slugs)
  {
    $addresses = DB::table('address_slug')
      ->whereIn('slug', array_values($slugs))
      ->get();

    $hotels = Hotel::with(['rooms', 'address']);
    $filter = [];
    foreach ($addresses as $address) {
      if ($slugs['city'] === $address->slug) {
        $filter['city'] = $address->address;
        $slugs['city'] = $address->address;
      }
      if (isset($slugs['district']) && $slugs['district'] === $address->slug) {
        $filter['city_district'] = $address->address;
        $slugs['district'] = $address->address;
      }
      if (isset($slugs['area']) && $slugs['area'] === $address->slug) {
        $filter['city_area'] = $address->address;
        $slugs['area'] = $address->address;
      }
      if (isset($slugs['street']) && $slugs['street'] === $address->slug) {
        $filter['street'] = $address->address;
        $slugs['street'] = $address->address;
      }
    }

    $hotels->whereHas('address', function ($builder) use ($filter) {
      foreach ($filter as $col => $value)
        if ($col !== 'metro')
          $builder->where($col, $value);
    });

    if (array_key_exists('metro', $slugs)) 
    {
      $metros = DB::table('metros')->get();

      $metro_found = false;

      foreach($metros as $metro) {
        if($slugs['metro'] == trim(Str::slug($metro->name))) 
        {
          $hotels->whereHas('metros', function ($builder) use ($metro) {
            $builder->where('name', $metro->name);
          });

          $slugs['metro'] = $metro->name;
          $metro_found = true;
        }
      }

      if($metro_found == false) {
        $hotels->where("id", 0); // if metro non exist query should fail
      }
    }

    return $hotels;
  }

  /**
   * @return Builder
   */
  public function getBuilder(): Builder
  {
    return $this->builder;
  }

  public function setBuilder(Builder $builder): Search
  {
    $this->builder = $builder;

    return $this;
  }

  public function setQuery($query = '', string $type = 'hotels'): Search
  {
    if (empty($query))
      return $this;
    if (!in_array($type, ['hotels', 'rooms']))
      return $this;

    $this->searchable = true;

    $query_array = $this->createQueryArray($query);
    foreach ($query_array as $like)
      $this->builder->orWhere($type . '.name', 'LIKE', $like)
        ->orWhere('addresses.value', 'LIKE', $like);

    return $this;
  }

  public function createQueryArray($query): array
  {
    if (empty($query))
      return [];
    $query_raw = explode(' ', $query);
    $query_array = array_map(function ($item) {
      return "%$item%";
    }, $query_raw);

    return $query_array;
  }

  public function setAttrs(array $attributes): Search
  {
    if (isset($attributes['hotel']) && count($attributes['hotel'])) {
      $this->builder->leftJoin('attribute_hotel', 'hotels.id', '=', 'attribute_hotel.hotel_id')
        ->whereIn('attribute_hotel.attribute_id', $attributes['hotel']);
      $this->searchable = true;
    } elseif (isset($attributes['room']) && count($attributes['room'])) {
      $this->builder->leftJoin('attribute_room', 'rooms.id', '=', 'attribute_room.room_id')
        ->whereIn('attribute_room.attribute_id', $attributes['room']);
      $this->searchable = true;
    }

    return $this;
  }

  public function hotOnly(bool $is_hot = true): Search
  {
    if ($is_hot)
      $this->builder->where('rooms.is_hot', true);
    return $this;
  }

  public function setAddress(array $address): Search
  {

    $columns = [
      'postal_code', 'country',
      'region', 'area',
      'city', 'street',
      'house', 'block',
      'flat', 'office',];

    foreach ($address as $reg => $value)
      if (!empty($value) && in_array($reg, $columns)) {
        $this->searchable = true;
        $this->builder->where('addresses.' . $reg, $value);
      }

    return $this;
  }

  public function getHotels()
  {
    $hotels = Hotel::with(['rooms', 'rooms.attrs', 'attrs', 'address']);

    $hotels_id = $this->builder->select('hotels.id')->get()->pluck('id')->toArray();
    if ($this->searchable)
      $hotels->whereIn('id', $hotels_id);

    return $hotels->get();
  }
}
