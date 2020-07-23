<?php declare(strict_types=1);

namespace App\CycleFetcher;

interface CycleFetcherInterface
{
    public function fetchCycles(): array;
}