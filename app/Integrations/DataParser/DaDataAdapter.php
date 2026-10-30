<?php

namespace App\Integrations\DataParser;

use App\Interfaces\DataParserInterface;
use Dadata\DadataClient;
use Illuminate\Support\Collection;
use RuntimeException;
use Throwable;

class DaDataAdapter implements DataParserInterface
{
    private function getClient(): DadataClient
    {
        $token = config('dadata.token');
        $secret_key = config('dadata.secret_key');

        if (! is_string($token) || $token === '' || ! is_string($secret_key) || $secret_key === '') {
            throw new RuntimeException('DaData token and secret key cannot be empty');
        }

        return new DadataClient($token, $secret_key);
    }

    /**
     * @return Collection<int, string>
     *
     * @throws RuntimeException
     */
    public function autoCompleteAddress(string $partAddress): Collection
    {
        try {
            $resultParse = collect($this->getClient()->suggest('address', $partAddress));

            return $resultParse->map(fn (array $result) => $result['value']);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException(
                'Не удалось получить подсказки адреса. Попробуйте позже.',
                0,
                $e,
            );
        }
    }
}
