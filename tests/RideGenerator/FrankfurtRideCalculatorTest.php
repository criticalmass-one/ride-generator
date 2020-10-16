<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\RideCalculator\FrankfurtRideCalculator;
use App\RideCalculator\RideCalculatorInterface;
use App\RideNamer\GermanCityDateRideNamer;
use App\RideNamer\RideNamerList;
use App\Model\City;
use App\Model\CityCycle;
use PHPUnit\Framework\TestCase;

class FrankfurtRideCalculatorTest extends TestCase
{
    public function testFrankfurtInAugust2019(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2019)
            ->setMonth(8)
            ->setCycle($this->createFrankfurtCycle())
            ->execute();

        $this->assertNotNull($ride);

        $this->assertEquals(new \DateTime('2019-08-09 19:00:00'), $ride->getDateTime());
    }

    public function testFrankfurtInSeptember2019(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2019)
            ->setMonth(9)
            ->setCycle($this->createFrankfurtCycle())
            ->execute();

        $this->assertNotNull($ride);

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
