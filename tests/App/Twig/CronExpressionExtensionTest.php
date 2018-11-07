<?php
declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\CronExpressionExtension;
use PHPUnit\Framework\TestCase;

class CronExpressionExtensionTest extends TestCase
{
    /** @var CronExpressionExtension */
    private $cronExpressionExtension;

    public function setUp()
    {
        $this->cronExpressionExtension = new CronExpressionExtension();
    }

    public function testGetFunctions()
    {
        $this->assertInstanceOf(\Twig_SimpleFunction::class, $this->cronExpressionExtension->getFunctions()[0]);
    }

    public function testCronExpression()
    {
        $this->assertInstanceOf(\DateTime::class, $this->cronExpressionExtension->cronExpression('* * * * 6'));
    }

    public function testGetName()
    {
        $this->assertSame(CronExpressionExtension::CRON_EXPRESSION, $this->cronExpressionExtension->getName());
    }
}
