<?php declare(strict_types=1);

namespace App\RideGenerator;

use Carbon\Carbon;

interface RideGeneratorInterface
{
    public function setDateTimeList(array $dateTimeList): RideGeneratorInterface;

    public function setDateTime(Carbon $dateTime): RideGeneratorInterface;

    public function addDateTime(Carbon $dateTime): RideGeneratorInterface;

    public function getRideList(): array;

    public function execute(): RideGeneratorInterface;
}
