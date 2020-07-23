<?php declare(strict_types=1);

namespace App\RideNamer;

use App\Model\Ride;

interface RideNamerInterface
{
    public function generateTitle(Ride $ride): string;
}
