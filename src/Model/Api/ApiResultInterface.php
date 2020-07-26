<?php declare(strict_types=1);

namespace App\Model\Api;

use App\Model\Ride;

interface ApiResultInterface
{
    public function getRide(): Ride;
}