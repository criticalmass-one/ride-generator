<?php declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class Ride
{
    /**
     * @JMS\Expose()
     */
    protected ?int $id = null;

    /**
     * @JMS\Expose()
     */
    protected ?CityCycle $cycle = null;

    /**
     * @JMS\Expose()
     */
    protected ?City $city = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $slug = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $title = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $description = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon<'U'>")
     */
    protected ?Carbon $dateTime = null;

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
     * @JMS\Type("Carbon<'U'>")
     */
    protected \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new Carbon();
    }

    public function setId(int $id): self
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCycle(): ?CityCycle
    {
        return $this->cycle;
    }

    public function setCycle(CityCycle $cityCycle = null): self
    {
        $this->cycle = $cityCycle;

        return $this;
    }

    public function setDateTime(\DateTime $dateTime = null): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
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

    public function setCity(City $city = null): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
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

    public function setSlug(string $slug = null): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function hasSlug(): bool
    {
        return $this->slug !== null;
    }

    public function setTitle(string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
