<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Images\UploadRequest;
use App\Models\Image;
use App\Services\Image\Service as ImageService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UploadRequest $request)
    {
        $data = $request->getData();
        $schema = config('app.imageables');
        $model = $schema[$data->item]['model']::findOrFail($data->id);
        Gate::authorize('update', $model);
        $catalogPath = $schema[$data->item]['catalog'];
        foreach ($data->images as $up) {
            $data = [
                'image' => $up,
                'catalogPath' => $catalogPath,
            ];
            $this->imageService->saveImage($model, $data);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        Gate::authorize('update', $image->imageable);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->back();
    }
}
