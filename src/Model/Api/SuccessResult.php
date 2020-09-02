<?php declare(strict_types=1);

namespace App\Model\Api;

use App\Model\Ride;

class SuccessResult extends AbstractApiResult
{
    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }
}
