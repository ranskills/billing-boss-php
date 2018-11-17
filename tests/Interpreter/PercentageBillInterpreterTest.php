<?php

namespace BillingBoss\Tests\Interpreter;


use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;
use BillingBoss\Interpreter\PercentageBillInterpreter;
use PHPUnit\Framework\TestCase;

class PercentageBillInterpreterTest extends TestCase
{
    /**
     * @var BillInterpreter
     */
    private $interpreter;

    public function setUp()
    {
        $this->interpreter = new PercentageBillInterpreter();
    }

    public function testshouldPassWithValidBillingStructure()
    {
        $context = new BillContext(0, '1%');
        $this->assertTrue($this->interpreter->isValid($context));

        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(0, $bill);

        $context->setAmount(1000);
        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(10, $bill);

        $context->setAmount(1000)->setStructure('1.5%');
        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(15, $bill);

        $context->setAmount(1000)->setStructure('0%');
        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(0, $bill);
    }


    public function testShouldNotBeAbleToInterpretInvalidBillingStructure()
    {
        $context = new BillContext(0, '5');
        $this->assertFalse($this->interpreter->isValid($context), 'Flat rate billing');

        $context->setStructure('');
        $this->assertFalse($this->interpreter->isValid($context), 'Empty billing structure');
    }

    public function testReturnABillOfZeroForInvalidBillingStructure()
    {
        $context = new BillContext(0, '5');
        $this->assertEquals(0, $this->interpreter->interpret($context));

        $context->setStructure('');
        $this->assertEquals(0, $this->interpreter->interpret($context));
    }


}