<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App\Traits;

use App\Helpers\Json;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function Illuminate\Events\queueable;

trait UploadImage
{
  public function upload(Request $request): ?JsonResponse
  {
    $path = false;
    if ($files = $request->allFiles()) {
      foreach ($files AS $file) {
        $path = '/storage/'.$file->store(date('Y/m/d'));
      }
    }
    if ($path)
      return response()->json(['location' => $path]);
    return abort(500);
  }

  /**
   * Delete Image
   *
   * @param Image $image
   *
   * @return JsonResponse
   */
  public function delete(Image $image): JsonResponse
  {
    try {
      if ($this->checkLastImage($image)) {
        return Json::answer(['error' => 'Нельзя удалить последнюю фотографию'], false, 200);
      }

      $image->delete();
      return Json::good(['deleted' => true]);
    } catch (\Exception $exception) {
      return Json::bad(['error' => $exception->getMessage()]);
    }
  }

  public function uploadFor(Request $request)
  {
    $modelName = $request->get('model_name');
    $modelID = $request->get('modelID');

    try {
      $modelName = '\\App\\Models\\'.$modelName;
      $model = $modelName::findOrFail($modelID);
      $images = Image::upload($request, $model);

      return Json::good(['images' => $images]);
    } catch (\Exception $exception) {
      return Json::bad(['error' => $exception->getMessage()]);
    }
  }

  private function checkLastImage (Image $image): bool
  {
    $model = $image->model_type;
    $object = $model::find($image->model_id);

    return $object->images()->count() === 1;
  }
}