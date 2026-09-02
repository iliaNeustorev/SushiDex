<?php

namespace App\Http\Resources\General;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

class GeneralPagination extends Data
{
    public function __construct(
        public int $page,
        public int $total,
        public int $lastPage,
        public int $perPage,
        public array $data
    ) {}

    public static function fromPaginator(LengthAwarePaginator $paginator, string $dataResourceClass)
    {
        return new static(
            $paginator->currentPage(),
            $paginator->total(),
            $paginator->lastPage(),
            $paginator->perPage(),
            $dataResourceClass::collect($paginator->items())
        );
    }
}
