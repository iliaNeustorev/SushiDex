<?php

namespace App\Services\Image;

use App\Jobs\ResizeImage;
use App\Models\Image;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Service
{
    public function saveImage(Model $model, array $data): bool
    {
        try {
            $this->checkModelOnImages($model);
            $file = $data['image'];
            $catalogPath = $data['catalogPath'];
            $pathFile = "images/$catalogPath/$model->id";
            $fileName = time().mt_rand(1000, 9999).'.'.$file->extension();
            $pathFileForSave = $pathFile.'/'.$fileName;
            $resultSaveFile = Storage::disk('public')->putFileAs($pathFile, $file, $fileName);
            if ($resultSaveFile) {
                $nameFile = preg_replace('#\..+$#', '', $file->getClientOriginalName());
                $newImage = $model->images()->create(['path' => $pathFileForSave, 'name' => $nameFile]);
                ResizeImage::dispatch($newImage);

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Не удалось сохранить картинку', [
                'class' => get_class($model),
                'model_id' => $model->id,
                'errorMessage' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function resizeImage(Image $image, array $data): bool
    {
        try {
            $driver = $data['driver'] ?? 'imagick';
            $newType = $data['newType'] ?? 'webp';
            $manager = FactoryInterventionImage::make($driver);
            $fullPath = Storage::disk('public')->path($image->path);
            $imageManager = $manager->read($fullPath);
            $imageManager->scale(300, 200);
            $imageManager->toWebp();
            $encoded = $imageManager->encode();
            $pathSave = $image->path;
            if (pathinfo($image->path, PATHINFO_EXTENSION) !== $newType) {
                Storage::disk('public')->delete($image->path);
                $pathSave = preg_replace('#\..+$#', ".$newType", $image->path);
            }
            Storage::disk('public')->put($pathSave, $encoded);
            if ($pathSave !== $image->path) {
                $image->update(['path' => $pathSave]);
            }

            return true;
        } catch (Exception $e) {
            Log::error('Не удалось изменить изображение.', ['imageId' => $image->id, 'errorMessage' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function deleteAllImagesModel(Model $model, array $data): void
    {
        $catalogPath = $data['catalogPath'];
        $this->checkModelOnImages($model);
        $images = $model->images()->get();
        if ($images->isNotEmpty()) {
            Storage::disk('public')->deleteDirectory("images/$catalogPath/$model->id");
            $model->images()->delete();
        }
    }

    /**
     * @throws Exception
     */
    protected function checkModelOnImages(Model $model): true
    {
        if (! method_exists($model, 'images')) {
            throw new Exception('Not found images model', 1);
        }

        return true;
    }
}
