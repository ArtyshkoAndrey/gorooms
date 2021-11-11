<?php
/**
 * Created by PhpStorm.
 * User: walft
 * Date: 17.07.2020
 * Time: 17:49
 */

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

trait UseImages
{

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'model')->orderBy('order', 'ASC');
    }

    public function image(): HasOne
    {
      return $this->hasOne(Image::class, 'model_id', 'id')
        ->where('model_type', '=', self::class)
        ->orderBy('order', 'ASC')
        ->withDefault([
          'path' => $this->no_image ?? Image::DEFAULT
        ]);
    }

    protected static function bootUseImages()
    {
        self::deleting(function ($model) {
            if ($model->images()->count()) {
                foreach ($model->images AS $image) {
                    @Storage::delete(str_replace('storage/', '',$image->path));
                }
                $model->images()->delete();
            }
        });
    }
}