<?php
declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\ContainsCronExpression;
use App\Validator\ContainsCronExpressionValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ContainsCronExpressionValidatorTest extends TestCase
{
    /** @var ContainsCronExpressionValidator */
    private $containsCronExpressionValidator;

    public function setUp()
    {
        $this->containsCronExpressionValidator = new ContainsCronExpressionValidator();
    }

    public function testValidateValidInput()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())
            ->method('buildViolation');
        $this->containsCronExpressionValidator->initialize($context);

        $this->containsCronExpressionValidator->validate('* * * * *', new ContainsCronExpression());
        $this->addToAssertionCount(1);
    }

    public function testValidateInvalidInput()
    {
        $constraintViolationBuilderInterfaceMock = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolationBuilderInterfaceMock
            ->expects($this->once())
            ->method('setParameter')
            ->willReturn($constraintViolationBuilderInterfaceMock);
        $constraintViolationBuilderInterfaceMock
            ->expects($this->once())
            ->method('addViolation')
            ->willReturn(null);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilderInterfaceMock);

        $this->containsCronExpressionValidator->initialize($context);
        $this->containsCronExpressionValidator->validate('asdf', new ContainsCronExpression());
        $this->addToAssertionCount(1);
    }
}
