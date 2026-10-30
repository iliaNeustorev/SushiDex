<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface DataParserInterface
{
    /**
     * @return Collection<int, string>
     */
    public function autoCompleteAddress(string $partAddress): Collection;
}
