<?php declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class CityCycle
{
    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;
    const DAY_SUNDAY = 0;

    const WEEK_FIRST = 1;
    const WEEK_SECOND = 2;
    const WEEK_THIRD = 3;
    const WEEK_FOURTH = 4;
    const WEEK_LAST = 0;

    /**
     * @JMS\Expose()
     */
    protected ?int $id = null;

    /**
     * @JMS\Expose()
     */
    protected ?City $city = null;

    /**
     * @JMS\Expose()
     */
    protected ?int $dayOfWeek = null;

    /**
     * @JMS\Expose()
     */
    protected ?int $weekOfMonth = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon")
     */
    protected ?Carbon $time = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $location = null;

    /**
     * @JMS\Expose()
     */
    protected ?float $latitude = null;

    /**
     * @JMS\Expose()
     */
    protected ?float $longitude = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon")
     */
    protected ?Carbon $createdAt = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon")
     */
    protected ?Carbon $updatedAt = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon")
     */
    protected ?Carbon $disabledAt = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon")
     */
    protected ?Carbon $validFrom = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon")
     */
    protected ?Carbon $validUntil = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $rideCalculatorFqcn = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $description = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $specialDayOfWeek = null;

    /**
     * @JMS\Expose()
     */
    private ?string $specialWeekOfMonth = null;

    public function __construct()
    {
        $this->createdAt = new Carbon();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setLatitude(float $latitude = null): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude = null): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setWeekOfMonth(int $weekOfMonth): self
    {
        $this->weekOfMonth = $weekOfMonth;

        return $this;
    }

    public function getWeekOfMonth(): ?int
    {
        return $this->weekOfMonth;
    }

    public function setTime(Carbon $time = null): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTime(): ?Carbon
    {
        return $this->time;
    }

    public function setLocation(string $location = null): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setCreatedAt(Carbon $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(Carbon $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function setDisabledAt(Carbon $disabledAt = null): self
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    public function getDisabledAt(): ?Carbon
    {
        return $this->disabledAt;
    }

    public function setValidFrom(Carbon $validFrom = null): self
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidFrom(): ?Carbon
    {
        return $this->validFrom;
    }

    public function setValidUntil(Carbon $validUntil = null): self
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    public function getValidUntil(): ?Carbon
    {
        return $this->validUntil;
    }

    public function hasRange(): bool
    {
        return ($this->validFrom && $this->validUntil);
    }

    public function getRideCalculatorFqcn(): ?string
    {
        return $this->rideCalculatorFqcn;
    }

    public function setRideCalculatorFqcn(?string $rideCalculatorFqcn): self
    {
        $this->rideCalculatorFqcn = $rideCalculatorFqcn;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function hasSpecialCalculator(): bool
    {
        return $this->rideCalculatorFqcn !== null;
    }

    public function getSpecialDayOfWeek(): ?string
    {
        return $this->specialDayOfWeek;
    }

    public function setSpecialDayOfWeek(?string $specialDayOfWeek): self
    {
        $this->specialDayOfWeek = $specialDayOfWeek;

        return $this;
    }

    public function getSpecialWeekOfMonth(): ?string
    {
        return $this->specialWeekOfMonth;
    }

    public function setSpecialWeekOfMonth(?string $specialWeekOfMonth): self
    {
        $this->specialWeekOfMonth = $specialWeekOfMonth;

        return $this;
    }
}
