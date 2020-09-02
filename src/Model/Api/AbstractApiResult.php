<?php declare(strict_types=1);

namespace App\Model\Api;

use App\Model\Ride;

class AbstractApiResult implements ApiResultInterface
{
    protected Ride $ride;

    public function setRide(Ride $ride): self
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }
}