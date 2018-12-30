<?php
declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\ValidatorExtension;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Twig\ValidatorExtension
 * @covers ::<!public>
 */
class ValidatorExtensionTest extends TestCase
{
    /** @var ValidatorExtension */
    private $validatorExtension;

    public function setUp()
    {
        $this->validatorExtension = new ValidatorExtension();
    }

    /**
     * @covers ::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertInstanceOf(\Twig_SimpleFunction::class, $this->validatorExtension->getFunctions()[0]);
    }

    /**
     * @covers ::isDateTime
     */
    public function testIsDateTimeFalse()
    {
        $this->assertFalse($this->validatorExtension->isDateTime('bla-bla-bla'));
    }

    /**
     * @covers ::isDateTime
     */
    public function testIsDateTimeTrue()
    {
        $this->assertTrue($this->validatorExtension->isDateTime(new \DateTime()));
    }
}
