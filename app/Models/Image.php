<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App\Models;

use App\Traits\CreatedAtOrdered;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Image
 *
 * @property int $id
 * @property string|string[] $path
 * @property string $name
 * @property bool $moderate
 * @property string|null $title
 * @property string|null $description
 * @property int|null $default
 * @property string|null $model_type
 * @property int|null $model_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Image newModelQuery()
 * @method static Builder|Image newQuery()
 * @method static Builder|Image query()
 * @method static Builder|Image whereCreatedAt($value)
 * @method static Builder|Image whereDefault($value)
 * @method static Builder|Image whereDescription($value)
 * @method static Builder|Image whereId($value)
 * @method static Builder|Image whereModelId($value)
 * @method static Builder|Image whereModelType($value)
 * @method static Builder|Image whereModerate($value)
 * @method static Builder|Image whereName($value)
 * @method static Builder|Image wherePath($value)
 * @method static Builder|Image whereTitle($value)
 * @method static Builder|Image whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Image extends Model
{
  use CreatedAtOrdered;

  public const DEFAULT = 'img/img-hotel.jpg';

  protected static string $orderDirection = 'ASC';
  public $casts = [
    'moderate' => 'boolean'
  ];
  protected $fillable = [
    'name',
    'path',
    'moderate',
    'order'
  ];

//  TODO: Переписать алгоритм загузки фотки что бы order становился автоматом последний
  public static function upload($request, &$uploadTo, $attr_name = 'image'): array
  {
    if ($request->hasFile($attr_name)) {
      $files = $request->file($attr_name);
      if (!is_array($files)) {
        $files = [$files];
      }
      $images = [];
      if ($uploadTo->images()->count() === 0) {
        $order = 1;
      } else {
        $order = $uploadTo->images->last()->order + 1;
      }
      foreach ($files as $file) {
        $path = $file->store(date('Y/m/d'));
        $image = new Image();
        $image->name = $file->getClientOriginalName();
        $image->path = 'storage/' . $path;
        $image->moderate = !(Auth::user()->is_moderate || Auth::user()->is_admin);
        $image->order = $order;
        $image->save();
        $images[] = $image;
        // self::watermark($uploadTo, $image);
        $order++;
      }
      $uploadTo->images()->saveMany($images);
      $uploadTo->save();
      return $images;
    }
    return [];
  }

  public static function watermark($model, Image $image): void
  {
    if (!($model instanceof Hotel || $model instanceof Room)) {
      return;
    }

    $path = str_replace('storage', 'app/public', $image->path);
    $img = \Intervention\Image\Facades\Image::make(storage_path($path));
    $img->insert(storage_path('images/watermark.png'), 'center');
    $img->save($image->path);
  }

//  public static function boot()
//  {
//    parent::boot();
//
//    self::updating(function (Image $image) {
//      self::beforeSave($image);
//    });
//
//    self::creating(function (Image $image) {
//      self::beforeSave($image);
//    });
//
//    self::saving(function (Image $image) {
//      self::beforeSave($image);
//    });
//
//    self::deleting(function (Image $image) {
//      if ($image->default) {
//        $model_id = $image->model_id;
//        $model_type = $image->model_type;
//        try {
//          $other_image = Image::where('model_id', $model_id)
//            ->where('model_type', $model_type)
//            ->whereNot('id', $image->id)
//            ->first();
//          $other_image->default = true;
//          $other_image->save();
//        } catch (Exception $exception) {
//          if (Image::where('model_id', $model_id)->where('model_type', $model_type)->count() > 0)
//            Log::error($exception);
//        }
//      }
//      File::delete($image->getRawOriginal('path'));
//    });
//  }

  public static function beforeSave(Image $image): void
  {
//    if ($image->default) {
//      $model_id = $image->model_id;
//      $model_type = $image->model_type;

//      self::where('model_id', $model_id)->where('model_type', $model_type)->update(['default' => false]);
//    }
  }

  /**
   * Меняем урлу картинки на урлу с оптимизацией
   * @param string $value
   * @return string|string[]
   */
  public function getPathAttribute(string $value)
  {
    return str_replace('storage', 'image', $value);
  }
}
