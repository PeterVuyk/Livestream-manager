<?php
declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsCronExpression extends Constraint
{
    public $message = 'The string "{{ string }}" is invalid and should be a valid cron expression';
}
