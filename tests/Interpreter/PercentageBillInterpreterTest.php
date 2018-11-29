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
        $context = new BillContext(0, '1%, 1 - *');
        $this->assertTrue($this->interpreter->isValid($context));

        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(-1));
        $this->assertEquals(0.0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(200));
        $this->assertEquals(2.0, $bill);


        $context = new BillContext(0, '1%, 1 - 500 | 3%, 501 - 2000 | 5%, 2001 - *');

        $bill = $this->interpreter->interpret($context->setAmount(500));
        $this->assertEquals(5.0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(1000));
        $this->assertEquals(30.0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(2001));
        $this->assertEquals(100.05, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(125000));
        $this->assertEquals(6250.0, $bill);
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