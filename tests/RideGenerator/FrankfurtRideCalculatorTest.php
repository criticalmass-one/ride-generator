<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\RideCalculator\FrankfurtRideCalculator;
use App\RideCalculator\RideCalculatorInterface;
use App\RideNamer\GermanCityDateRideNamer;
use App\RideNamer\RideNamerList;
use App\Model\City;
use App\Model\CityCycle;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

        $this->assertEquals(new Carbon('2019-08-09 19:00:00', new CarbonTimeZone('Europe/Berlin')), $ride->getDateTime());
    }

    public function testFrankfurtInSeptember2019(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2019)
            ->setMonth(9)
            ->setCycle($this->createFrankfurtCycle())
            ->execute();

        $this->assertNotNull($ride);

        $this->assertEquals(new Carbon('2019-09-06 19:00:00', new CarbonTimeZone('Europe/Berlin')), $ride->getDateTime());
    }

    protected function getRideCalculator(): RideCalculatorInterface
    {
        $rideNamerList = new RideNamerList();
        $rideNamerList->addRideNamer(new GermanCityDateRideNamer());

        return new FrankfurtRideCalculator($rideNamerList, $this->createValidator());
    }

    protected function createFrankfurtCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setName('Frankfurt')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setTime(new Carbon('19:00:00'))
            ->setLocation('Opernplatz')
            ->setLatitude(50.115446)
            ->setLongitude(8.671593)
            ->setCity($city);

        return $cityCycle;
    }

    protected function createValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }
}
