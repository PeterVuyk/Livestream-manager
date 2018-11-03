<?php
declare(strict_types=1);

namespace App\Validator;

use Cron\CronExpression;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class ContainsCronExpressionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!isset($value)) {
            return;
        }

        try {
            $cron = CronExpression::factory($value);
            if ($cron instanceof CronExpression) {
                return;
            }
        } catch (\InvalidArgumentException $exception) {
            //Ignore, will be picked up by the build violation.
        }
        $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
    }
}
