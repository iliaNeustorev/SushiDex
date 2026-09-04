<?php

namespace App\Services\Product;

use App\Http\RequestDTO\Product\Admin\ProductsSaveReqDTO;
use App\Models\Product;
use App\Services\Image\Service as ImageService;

readonly class ProductAdminService
{
    public function __construct(private ImageService $imageService)
    {
    }

    public function create(ProductsSaveReqDTO $data): Product
    {
        return Product::create($data->toArray());
    }

    public function update(Product $product, ProductsSaveReqDTO $data): Product
    {
        $product->update($data->toArray());

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
        $this->imageService->deleteAllImagesModel($product, ['catalogPath' => 'products']);
    }
}
