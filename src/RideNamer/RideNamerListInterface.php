<?php declare(strict_types=1);

namespace App\RideNamer;

interface RideNamerListInterface
{
    public function addRideNamer(RideNamerInterface $rideNamer): RideNamerListInterface;

    public function getList(): array;

    public function getRideNamerByFqcn(string $rideNamerFqcn): ?RideNamerInterface;
}
