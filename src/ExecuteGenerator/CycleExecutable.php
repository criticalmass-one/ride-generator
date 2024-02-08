<?php declare(strict_types=1);

namespace App\ExecuteGenerator;

use App\Model\City;
use App\Model\CityCycle;
use App\Validator\Constraint\ExecutorDateTime;
use Carbon\Carbon;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ExecutorDateTime
 */
class CycleExecutable
{
    #[JMS\Expose]
    protected ?string $citySlug = null;

    #[JMS\Expose]
    protected ?City $city = null;

    #[JMS\Expose]
    protected ?CityCycle $cityCycle = null;

    #[JMS\Expose]
    #[JMS\Type("Carbon<'U'>")]
    #[Assert\GreaterThanOrEqual('1992-09-01', message: 'Vor September 1992 können keine Touren angelegt werden — das ist übrigens das Datum der allerersten Critical Mass in San Francisco.')]
    #[OA\Property(type: 'datetime', description: 'Begin of time span to create rides.')]
    protected ?Carbon $fromDate = null;

    #[JMS\Expose]
    #[JMS\Type("Carbon<'U'>")]
    #[Assert\LessThanOrEqual('+1 years', message: 'Touren können maximal zwölf Monate im Voraus angelegt werden.')]
    #[OA\Property(type: 'datetime', description: 'End of time span to create rides.')]
    protected ?Carbon $untilDate = null;

    public function __construct()
    {
        $this->fromDate = new Carbon();
        $this->untilDate = new Carbon();
    }

    public function getFromDate(): ?Carbon
    {
        return $this->fromDate;
    }

    public function setFromDate(Carbon $fromDate = null): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getUntilDate(): ?Carbon
    {
        return $this->untilDate;
    }

    public function setUntilDate(Carbon $untilDate = null): self
    {
        $this->untilDate = $untilDate;

        return $this;
    }

    public function setCitySlug(?string $citySlug): self
    {
        $this->citySlug = $citySlug;

        return $this;
    }

    public function getCitySlug(): ?string
    {
        return $this->citySlug;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCityCycle(): ?CityCycle
    {
        return $this->cityCycle;
    }

    public function setCityCycle(?CityCycle $cityCycle): self
    {
        $this->cityCycle = $cityCycle;

        return $this;
    }
}
