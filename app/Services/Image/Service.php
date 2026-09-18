<?php

namespace App\Services\Image;

use App\Jobs\ResizeImage;
use App\Models\Image;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
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
            $resultSave = $this->saveNewImage($pathFile, $file);
            if ($resultSave['resultSaveFile']) {
                $nameFile = preg_replace('#\..+$#', '', $file->getClientOriginalName());
                $newImage = $model->images()->create(['path' => $resultSave['pathFile'], 'name' => $nameFile]);
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
        if (!method_exists($model, 'images')) {
            throw new Exception('Not found images model', 1);
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function saveOneImage(Model $model, array $data): bool
    {
        $newPath = '';
        try {
            $checkDelete = $this->deleteOneImage($model);
            if (!$checkDelete) {
                throw new Exception('Не удалось удалить файл', 1);
            }
            $this->checkModelOnImage($model);
            $file = $data['image'];
            $catalogPath = $data['catalogPath'];
            $pathFile = "images/$catalogPath/id_$model->id";
            $resultSave = $this->saveNewImage($pathFile, $file);
            $newPath = $resultSave['pathFile'];
            if ($resultSave['resultSaveFile']) {
                $nameFile = preg_replace('#\..+$#', '', $file->getClientOriginalName());
                $newImage = $model->image()->updateOrCreate([], ['path' => $resultSave['pathFile'], 'name' => $nameFile]);
                ResizeImage::dispatch($newImage);

                return true;
            }
        } catch (Exception $e) {
            Log::error('Не удалось сохранить картинку', [
                'class' => get_class($model),
                'model_id' => $model->id,
                'errorMessage' => $e->getMessage(),
            ]);
            Storage::disk('public')->delete($newPath);
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function deleteOneImage(Model $model): bool
    {
        $this->checkModelOnImage($model);
        $image = $model->image;
        if (isset($image)) {
            Storage::disk('public')->delete("$image->path");
            $checkFile = Storage::disk('public')->exists("$image->path");
            if ($checkFile) {
                Log::error('Не удалось удалить файл.', ['path' => $image->path]);
                return false;
            }
            $image->delete();
        }
        return true;
    }

    /**
     * @throws Exception
     */
    protected function checkModelOnImage(Model $model): true
    {
        if (!method_exists($model, 'image')) {
            throw new Exception('Not found image model', 1);
        }

        return true;
    }

    protected function saveNewImage(string $pathFile, UploadedFile $file): array
    {
        $fileName = time() . mt_rand(1000, 9999) . '.' . $file->extension();
        $pathFileForSave = $pathFile . '/' . $fileName;
        return [
            'resultSaveFile' => Storage::disk('public')->putFileAs($pathFile, $file, $fileName),
            'pathFile' => $pathFileForSave,
        ];
    }
}
