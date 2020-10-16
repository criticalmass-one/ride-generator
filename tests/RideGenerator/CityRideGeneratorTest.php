<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\Model\City;
use App\Model\CityCycle;
use App\Model\Ride;
use App\RideCalculator\FrankfurtRideCalculator;
use App\RideCalculator\RideCalculatorManager;
use App\RideGenerator\CityRideGenerator;
use App\RideGenerator\CityRideGeneratorInterface;
use App\RideNamer\GermanCityDateRideNamer;
use App\RideNamer\RideNamerList;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CityRideGeneratorTest extends TestCase
{
    public function testRideGeneratorForHamburgInJune2011(): void
    {
        $dateTime = new Carbon('2011-06');

        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideGenerator = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg));

        $rideList = $rideGenerator
            ->setDateTime($dateTime)
            ->addCity($hamburg)
            ->execute()
            ->getRideList();

        $this->assertCount(1, $rideList);

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new Carbon('2011-06-24 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 24.06.2011', $ride->getTitle());
    }

    public function testRideGeneratorForHamburgInSummer2011(): void
    {
        $dateTimeList = [
            new Carbon('2011-06'),
            new Carbon('2011-07'),
            new Carbon('2011-08'),
        ];

        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideGenerator = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg));

        $rideList = $rideGenerator
            ->setDateTimeList($dateTimeList)
            ->addCity($hamburg)
            ->execute()
            ->getRideList();

        $this->assertCount(3, $rideList);

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new Carbon('2011-08-26 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 26.08.2011', $ride->getTitle());

        $ride = array_pop($rideList);

        $this->assertEquals(new Carbon('2011-07-29 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 29.07.2011', $ride->getTitle());

        $ride = array_pop($rideList);

        $this->assertEquals(new Carbon('2011-06-24 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 24.06.2011', $ride->getTitle());
    }

    public function testRideGeneratorFor7RidesInHamburgIn2011(): void
    {
        $dateTimeList = [
            new Carbon('2011-01'),
            new Carbon('2011-02'),
            new Carbon('2011-03'),
            new Carbon('2011-04'),
            new Carbon('2011-05'),
            new Carbon('2011-06'),
            new Carbon('2011-07'),
            new Carbon('2011-08'),
            new Carbon('2011-09'),
            new Carbon('2011-10'),
            new Carbon('2011-11'),
            new Carbon('2011-12'),
        ];

        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideGenerator = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg));

        $rideList = $rideGenerator
            ->setDateTimeList($dateTimeList)
            ->addCity($hamburg)
            ->execute()
            ->getRideList();

        $this->assertCount(7, $rideList);
    }

    public function testNoRideBeforeValidAfterInHamburgAt201102(): void
    {
        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideList = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg))
            ->addCity($hamburg)
            ->setDateTime(new Carbon('2011-02-01'))
            ->execute()
            ->getRideList();

        $this->assertCount(0, $rideList);
    }

    public function testRideGeneratorForFrankfurtInJune2019(): void
    {
        $dateTime = new Carbon('2019-06');

        $frankfurt = new City();
        $frankfurt->setTitle('Critical Mass Frankfurt');

        $rideGenerator = $this->createPreparedRideGeneratorFor($frankfurt, $this->createCityCycleForFrankfurt($frankfurt));

        $rideList = $rideGenerator
            ->setDateTime($dateTime)
            ->addCity($frankfurt)
            ->execute()
            ->getRideList();

        $this->assertCount(2, $rideList);

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new Carbon('2019-06-02 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Opernplatz', $ride->getLocation());
        $this->assertEquals('50.115446', $ride->getLatitude());
        $this->assertEquals('8.671593', $ride->getLongitude());
        $this->assertEquals('Critical Mass Frankfurt 02.06.2019', $ride->getTitle());

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new Carbon('2019-06-07 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Opernplatz', $ride->getLocation());
        $this->assertEquals('50.115446', $ride->getLatitude());
        $this->assertEquals('8.671593', $ride->getLongitude());
        $this->assertEquals('Critical Mass Frankfurt 07.06.2019', $ride->getTitle());
    }

    protected function createPreparedRideGeneratorFor(City $city, array $cityCycleList): CityRideGeneratorInterface
    {
        $rideNamerList = new RideNamerList();
        $rideNamerList->addRideNamer(new GermanCityDateRideNamer());

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        //$validator = $this->createMock(ValidatorInterface::class);
        //$validator->method('validate')->willReturn(new ConstraintViolationList());

        return new CityRideGenerator($rideNamerList, $validator, new RideCalculatorManager());
    }

    protected function createCityCycleForHamburg(City $city): array
    {
        return [(new CityCycle())
            ->setCity($city)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new Carbon('19:00'))
            ->setLocation('Moorweide')
            ->setLatitude(53.562619)
            ->setLongitude(9.992445)
            ->setValidFrom(new Carbon('2011-06-24'))
            ->setValidUntil(new Carbon('2020-02-24'))];
    }

    protected function createCityCycleForFrankfurt(City $city): array
    {
        $cycle1 = (new CityCycle())
            ->setCity($city)
            ->setDayOfWeek(CityCycle::DAY_SUNDAY)
            ->setWeekOfMonth(CityCycle::WEEK_FIRST)
            ->setTime(new Carbon('19:00:00'))
            ->setLocation('Opernplatz')
            ->setLatitude(50.115446)
            ->setLongitude(8.671593);

        $cycle2 = (new CityCycle())
            ->setCity($city)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new Carbon('19:00:00'))
            ->setLocation('Opernplatz')
            ->setLatitude(50.115446)
            ->setLongitude(8.671593)
            ->setRideCalculatorFqcn(FrankfurtRideCalculator::class);

        return [$cycle2, $cycle1];
    }
}
