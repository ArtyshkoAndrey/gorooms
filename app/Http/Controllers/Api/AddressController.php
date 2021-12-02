<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Json;
use App\Models\Metro;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class AddressController extends Controller
{
  public function helper(Request $request): JsonResponse
  {
    if ($city = $request->get('city', false)) {
      $address = Address::where('city', $city);
      if ($district = $request->get('district', $request->get('city_district', false)))
        $address->where('city_district', $district);
      if ($area = $request->get('area', $request->get('city_area', false)))
        $address->where('city_area', $area);
      if ($street = $request->get('street', false))
        $address->where('street', $street);

      $hotels = $address->get()->map(function ($item) {
      //  dump($item->hotel_id);
        if ($item->hotel_id) {
          return $item->hotel_id;
        }
      });
      $collection = $this->modifyCollection($address->get());
      $metros = Metro::select('name')
        ->whereIn('hotel_id', $hotels)
        ->groupBy('name')
        ->get()
        ->sortBy('name')
        ->pluck('name');
    } else {
      $metros = Metro::all()
        ->sortBy('name')
        ->pluck('name');
      $collection = $this->modifyCollection(Address::all());
    }
    return Json::good([
      'metros' => $metros->unique(),
      'cities' => $collection->sortBy('city')->pluck('city')->unique(),
      'districts' => $collection->sortBy('city_district')->pluck('city_district')->unique(),
      'streets' => $collection->sortBy('street')->pluck('street')->unique(),
      'areas' => $collection->sortBy('city_area')->pluck('city_area')->unique(),
      'areas_short' => $collection->sortBy('city_area_short')->pluck('city_area_short')->unique(),
      'addresses' => $collection,
    ]);
  }

  private function modifyCollection(Collection $collection): Collection
  {
    return $collection->each(function ($item) {
      $areas = explode('-', $item->city_area);
      $area = '';
      foreach ($areas as $area_prefix)
        $area .= mb_substr($area_prefix, 0, 1);
      $area = mb_strtoupper($area) . 'АО';
      $item->city_area_short = $area;
    });
  }

}
