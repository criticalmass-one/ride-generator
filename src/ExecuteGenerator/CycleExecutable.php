<?php declare(strict_types=1);

namespace App\ExecuteGenerator;

use App\Model\City;
use App\Model\CityCycle;
use App\Validator\Constraint\ExecutorDateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExecutorDateTime
 */
class CycleExecutable
{
    protected ?City $city = null;

    protected ?CityCycle $cityCycle = null;

    /**
     * @var \DateTime $fromDate
     * @Assert\GreaterThanOrEqual("1992-09-01", message="Vor September 1992 können keine Touren angelegt werden — das ist übrigens das Datum der allerersten Critical Mass in San Francisco.")
     */
    protected $fromDate;

    /**
     * @var \DateTime $untilDate
     * @Assert\LessThanOrEqual("+1 years", message="Touren können maximal zwölf Monate im Voraus angelegt werden.")
     */
    protected $untilDate;

    public function __construct()
    {
        $this->fromDate = new \DateTime();
        $this->untilDate = new \DateTime();
    }

    public function getFromDate(): ?\DateTime
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTime $fromDate = null): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getUntilDate(): ?\DateTime
    {
        return $this->untilDate;
    }

    public function setUntilDate(\DateTime $untilDate = null): self
    {
        $this->untilDate = $untilDate;

        return $this;
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
