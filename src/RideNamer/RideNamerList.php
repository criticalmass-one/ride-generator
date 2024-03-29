<?php declare(strict_types=1);

namespace App\RideNamer;

class RideNamerList implements RideNamerListInterface
{
    /** @var array $list */
    protected $list;

    public function addRideNamer(RideNamerInterface $rideNamer): RideNamerListInterface
    {
        $this->list[$rideNamer::class] = $rideNamer;
        
        return $this;
    }
    
    public function getList(): array
    {
        return $this->list;
    }

    public function getRideNamerByFqcn(string $rideNamerFqcn): ?RideNamerInterface
    {
        if (array_key_exists($rideNamerFqcn, $this->list)) {
            return $this->list[$rideNamerFqcn];
        }

        return null;
    }
}
