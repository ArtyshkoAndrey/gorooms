<?php

namespace App\Helpers;

use Str;
use App\Models\Address;

class SeoData
{
  public ?string $url;
  public string $title;
  public string $h1;
  public string $description;
  public ?string $metro = null;
  public string $lastOfType = '';
  public Address $address;

  public function __construct(Address $address, $url = null)
  {
    $this->address = $address;
    $this->url = $url;

  }

  public function generate(): bool
  {
    if ($this->url && $this->metro !== '') {

      if ($this->lastOfType === 'metro') {
        $this->title = 'Отели на час | Гостиницы на Час Ночь у метро ' . $this->metro;
        $this->h1 = 'Гостиницы и отели у метро ' . $this->metro . ' — ' . $this->address->city;
        $this->description = 'Забронируйте номер на Час | Ночь | Сутки около метро ' . $this->metro . ' ▶Без комиссий и посредников ▶Фотографии и описание номеров ▶Удобный поиск отелей!';

      } else if ($this->lastOfType === 'district') {
        $this->title = 'Номера на Час Ночь | Почасовые отели ' . $this->address->city_district . ' район - ' . $this->address->city;
        if ($this->address->city_area) {
          $this->h1 = 'Отели и номера в ' . $this->address->city_area . ' в ' . $this->address->city_district . ' районе - ' . $this->address->city;
        } else {
          $this->h1 = 'Отели и номера в ' . $this->address->city_district . ' районе - ' . $this->address->city;
        }
        $this->description = Str::ucfirst($this->address->city_district) .' район, выбирайте и бронируйте гостиницу с почасовой оплатой номера в компании GoRooms ▶ Фото Номеров ▶Удобный поиск ▶ Подробное описание';
      } else if ($this->lastOfType === 'area') {
        $this->title = 'Отель на час|ночь|сутки в ' . $this->address->city_area . ' ' . $this->address->city . ' | Лучшие Цены';
        $this->h1 = 'Гостиницы и отели на час, ночь, сутки в ' . $this->address->city_area . ' ' . $this->address->city;
        $this->description = 'Снять номер на Час Ночь или Сутки в ' . $this->address->city_area . ', бронирование без комиссий, только актуальные фотографии и цены, самая большая база почасовых отелей в ' . $this->address->city_area . '.';
      } else if ($this->lastOfType === 'city') {
        $this->title = 'Отели на Чаc Ночь Сутки | Почасовые Гостиницы в ' . $this->address->city;
        $this->h1 = 'Все почасовые отели города ' . $this->address->city;
        $this->description = 'Ищете гостиницу в ' . $this->address->city . '? Компания Gorooms поможет подобрать номер в отеле на час ночь или  сутки недорого ▶ Без комиссий и посредников ▶ Низкие цены ▶ Бронируйте уже сейчас!';
      }

        return true;
    }
    return false;
  }
}