<?php
declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;

class Validator extends AbstractExtension
{
    const IS_DATE_TIME = 'isDateTime';

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(self::IS_DATE_TIME, [$this, self::IS_DATE_TIME], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param mixed $dateTime
     * @return bool
     */
    public function isDateTime($dateTime): bool
    {
        if ($dateTime instanceof \DateTime) {
            return true;
        }
        return false;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::IS_DATE_TIME;
    }
}
