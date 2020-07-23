<?php declare(strict_types=1);

namespace App\RideGenerator;

use App\Entity\City;

interface CityRideGeneratorInterface extends RideGeneratorInterface
{
    public function addCity(City $city): RideGeneratorInterface;

    public function setCityList(array $cityList): RideGeneratorInterface;
}