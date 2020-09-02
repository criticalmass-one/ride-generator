<?php declare(strict_types=1);

namespace App\Validator\Constraint;

use App\Validator\RideDateTimeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RideDateTime extends Constraint
{
    public $message = 'Das Startdatum muss vor dem Enddatum liegen';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return RideDateTimeValidator::class;
    }
}
