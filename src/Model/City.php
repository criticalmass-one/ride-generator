<?php declare(strict_types=1);

namespace App\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class City
{
    /**
     * @JMS\Expose()
     */
    protected ?int $id = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $name = null;

    /**
     * @JMS\Expose()
     */
    protected ?string $title = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("array<App\Model\CitySlug>")
     */
    protected array $slugs = [];

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSlugs(): array
    {
        return $this->slugs;
    }
}
