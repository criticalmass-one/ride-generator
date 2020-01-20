<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\Criticalmass\RideGenerator\RideCalculator\FrankfurtRideCalculator;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use App\Criticalmass\RideNamer\GermanCityDateRideNamer;
use App\Criticalmass\RideNamer\RideNamerList;
use App\Entity\City;
use App\Entity\CityCycle;
use PHPUnit\Framework\TestCase;

class FrankfurtRideCalculatorTest extends TestCase
{
    public function testFrankfurtInAugust2019(): void
    {
        $rideList = $this->getRideCalculator()
            ->setDateTime(new \DateTime('2019-08-01'))
            ->addCycle($this->createFrankfurtCycle())
            ->execute()
            ->getRideList();

        $this->assertEquals(1, count($rideList));

        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2019-08-09 19:00:00'), $ride->getDateTime());
    }

    public function testFrankfurtInSeptember2019(): void
    {
        $rideList = $this->getRideCalculator()
            ->setDateTime(new \DateTime('2019-09-01'))
            ->addCycle($this->createFrankfurtCycle())
            ->execute()
            ->getRideList();

        $this->assertEquals(1, count($rideList));

        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2019-09-06 19:00:00'), $ride->getDateTime());
    }

    protected function getRideCalculator(): RideCalculatorInterface
    {
        $rideNamerList = new RideNamerList();
        $rideNamerList->addRideNamer(new GermanCityDateRideNamer());

        return new FrankfurtRideCalculator($rideNamerList);
    }

    protected function createFrankfurtCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setCity('Frankfurt')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setTime(new \DateTime('19:00:00'))
            ->setLocation('Opernplatz')
            ->setLatitude(50.115446)
            ->setLongitude(8.671593)
            ->setCity($city);

        return $cityCycle;
    }
}
