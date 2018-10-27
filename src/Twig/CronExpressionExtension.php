<?php
declare(strict_types=1);

namespace App\Twig;

use Cron\CronExpression;
use Twig\Extension\AbstractExtension;

class CronExpressionExtension extends AbstractExtension
{
    const CRON_EXPRESSION = 'cronExpression';

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(self::CRON_EXPRESSION, [$this, self::CRON_EXPRESSION], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param string $expression
     * @return string
     */
    public function cronExpression(string $expression): string
    {
        $cron = CronExpression::factory($expression);
        return $cron->getNextRunDate()->format('D M j, G:i:s');
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::CRON_EXPRESSION;
    }
}
