<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\RideCalculator\RideCalculator;
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

class RideCalculatorTest extends TestCase
{
    public function testCalculatedRideIsNotNull(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->setCycle($this->createHamburgCycle())
            ->execute();

        $this->assertNotNull($ride);
    }

    public function testLocation(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->setCycle($this->createHamburgCycle())
            ->execute();

        $this->assertEquals('Stadtpark Hamburg', $ride->getLocation());
        $this->assertEquals(53.596812, $ride->getLatitude());
        $this->assertEquals(10.011008, $ride->getLongitude());
    }

    public function testTimezone(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->setCycle($this->createHamburgCycle())
            ->execute();

        $this->assertEquals(new \DateTimeZone('Europe/Berlin'), $ride->getDateTime()->getTimezone());
    }

    public function testLondon(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->setCycle($this->createLondonCycle())
            ->execute();

        $europeLondon = new \DateTimeZone('Europe/London');
        $europeBerlin = new \DateTimeZone('Europe/Berlin');

        $this->assertEquals($europeLondon, $ride->getDateTime()->getTimezone());
        $this->assertEquals((new \DateTime('2018-09-28 18:00:00', $europeLondon))->format('Y-m-d H:i:s'), $ride->getDateTime()->format('Y-m-d H:i:s'));
    }

    public function testTime(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(9)
            ->setCycle($this->createHamburgCycle())
            ->execute();

        $utc = new \DateTimeZone('UTC');
        $europeBerlin = new \DateTimeZone('Europe/Berlin');

        $this->assertEquals(new \DateTime('2018-09-28 17:00:00', $utc), $ride->getDateTime());
        $this->assertEquals($europeBerlin, $ride->getDateTime()->getTimezone());
    }

    public function testDaylightSavingTime(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(2)
            ->setCycle($this->createHamburgCycle())
            ->execute();

        $europeBerlin = new \DateTimeZone('Europe/Berlin');

        $this->assertEquals(new \DateTime('2018-02-23 19:00:00', $europeBerlin), $ride->getDateTime());
        $this->assertEquals($europeBerlin, $ride->getDateTime()->getTimezone());
    }

    public function testNoRideBeforeValidFromInHalle(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(2)
            ->setCycle($this->createHalleCycle())
            ->execute();

        $this->assertNull($ride);
    }

    public function testOneRideAfterValidFromInHalle(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2018)
            ->setMonth(3)
            ->setCycle($this->createHalleCycle())
            ->execute();

        $this->assertNotNull($ride);
    }

    public function testNoRideAfterValidUntilInRendsburg(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2019)
            ->setMonth(12)
            ->setCycle($this->createRendsburgCycle())
            ->execute();

        $this->assertNull($ride);
    }

    public function testOneRideBeforeValidUntilInRendsburg(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2019)
            ->setMonth(11)
            ->setCycle($this->createRendsburgCycle())
            ->execute();

        $this->assertNotNull($ride);
    }

    public function testNoRideBeforeValidFromInHarburgInJanuary(): void
    {
        $ride = $this->getRideCalculator()
            ->setYear(2019)
            ->setMonth(01)
            ->setCycle($this->createHarburgCycle())
            ->execute();

        $this->assertNull($ride);
    }

    protected function getRideCalculator(): RideCalculatorInterface
    {
        $rideNamerList = new RideNamerList();
        $rideNamerList->addRideNamer(new GermanCityDateRideNamer());

        return new RideCalculator($rideNamerList, $this->createValidator());
    }

    protected function createLondonCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setName('London')
            ->setTimezone('Europe/London');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new Carbon('18:00:00', new CarbonTimeZone('Europe/London')))
            ->setLocation('Southbank under Waterloo Bridge')
            ->setLatitude(51.507320112865)
            ->setLongitude(-0.11578559875488)
            ->setCity($city);

        return $cityCycle;
    }

    protected function createHamburgCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setName('Hamburg')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new Carbon('19:00:00'))
            ->setLocation('Stadtpark Hamburg')
            ->setLatitude(53.596812)
            ->setLongitude(10.011008)
            ->setCity($city);

        return $cityCycle;
    }

    protected function createHalleCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setName('Halle')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_FIRST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new Carbon('18:00:00'))
            ->setLocation('August-Bebel-Platz')
            ->setLatitude(51.491664696772)
            ->setLongitude(11.96897149086)
            ->setCity($city)
            ->setValidFrom(new Carbon('2018-03-01'));

        return $cityCycle;
    }

    protected function createRendsburgCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setName('Rendsburg')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new Carbon('19:00:00'))
            ->setLocation('Lornsendenkmal')
            ->setLatitude(54.300527)
            ->setLongitude(9.664402)
            ->setCity($city)
            ->setValidUntil(new Carbon('2019-12-12'));

        return $cityCycle;
    }

    protected function createHarburgCycle(): CityCycle
    {
        $city = new City();
        $city
            ->setName('Harburg')
            ->setTimezone('Europe/Berlin');

        $cityCycle = new CityCycle();
        $cityCycle
            ->setWeekOfMonth(CityCycle::WEEK_SECOND)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setTime(new Carbon('19:00:00'))
            ->setLocation('Karstadt')
            ->setLatitude(53.461030)
            ->setLongitude(9.978549)
            ->setCity($city)
            ->setValidFrom(new Carbon('2019-01-20'))
            ->setValidUntil(new Carbon('2019-12-04'));

        return $cityCycle;
    }

    protected function createValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }
}
