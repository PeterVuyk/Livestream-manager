<?php
declare(strict_types=1);

namespace App\Tests\App\Twig;

use App\Twig\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /** @var Validator */
    private $validator;

    public function setUp()
    {
        $this->validator = new Validator();
    }

    public function testGetFunctions()
    {
        $this->assertInstanceOf(\Twig_SimpleFunction::class, $this->validator->getFunctions()[0]);
    }

    public function testIsDateTimeFalse()
    {
        $this->assertFalse($this->validator->isDateTime('bla-bla-bla'));
    }

    public function testIsDateTimeTrue()
    {
        $this->assertFalse($this->validator->isDateTime('2008-03-09 16:05:07.123'));
    }
}
