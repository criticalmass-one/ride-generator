<?php declare(strict_types=1);

namespace App\Api;

interface RideApiInterface
{
    public function getRideListInMonth(\DateTime $dateTime): array;
}
