<?php declare(strict_types=1);

namespace App\ExecuteGenerator;

use App\Validator\Constraint\ExecutorDateTime;
use Carbon\Carbon;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ExecutorDateTime
 */
class CycleExecutable
{
    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon<'U'>")
     * @SWG\Property(type="datetime", description="Begin of time span to create rides.")
     * @Assert\GreaterThanOrEqual("1992-09-01", message="Vor September 1992 können keine Touren angelegt werden — das ist übrigens das Datum der allerersten Critical Mass in San Francisco.")
     */
    protected ?Carbon $fromDate = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("Carbon<'U'>")
     * @SWG\Property(type="datetime", description="End of time span to create rides.")
     * @Assert\LessThanOrEqual("+1 years", message="Touren können maximal zwölf Monate im Voraus angelegt werden.")
     */
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
}
