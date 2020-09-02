<?php declare(strict_types=1);

namespace App\Validator;

use App\Model\Ride;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RideDateTimeValidator extends ConstraintValidator
{
    /**
     * @param Ride $value
     */
    public function validate($value, Constraint $constraint): bool
    {
        $cityCycle = $value->getCycle();
        $dateTime = $value->getDateTime();

        return ($cityCycle->getValidFrom() <= $dateTime && $cityCycle->getValidUntil() >= $dateTime) ||
            ($cityCycle->getValidFrom() <= $dateTime && $cityCycle->getValidUntil() === null) ||
            ($cityCycle->getValidFrom() === null && $cityCycle->getValidUntil() >= $dateTime);
    }
}