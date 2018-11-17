<?php

namespace BillingBoss\Tests;


use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;
use BillingBoss\Interpreter\FlatRateBillInterpreter;
use PHPUnit\Framework\TestCase;

class FlatRateBillInterpreterTest extends TestCase
{
    /**
     * @var BillInterpreter
     */
    private $interpreter;

    public function setUp()
    {
        $this->interpreter = new FlatRateBillInterpreter();
    }

    public function testShouldPassWithValidBillingStructure()
    {
        $context = new BillContext(0, '5');
        $this->assertTrue($this->interpreter->isValid($context));

        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(5, $bill);

        $context->setAmount(1000);
        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(5, $bill);

        $context->setAmount(1000)->setStructure('10');
        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(10, $bill);

        $context->setAmount(1000)->setStructure('0');
        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(0, $bill);

    }


    public function testShouldNotBeAbleToInterpretInvalidBillingStructure()
    {
        $context = new BillContext(0, '5%');
        $this->assertFalse($this->interpreter->isValid($context), 'Perentage billing');

        $context->setStructure('');
        $this->assertFalse($this->interpreter->isValid($context), 'Empty billing structure');
    }

    public function testReturnABillOfZeroForInvalidBillingStructure()
    {
        $context = new BillContext(0, '5%');
        $this->assertEquals(0, $this->interpreter->interpret($context));

        $context->setStructure('');
        $this->assertEquals(0, $this->interpreter->interpret($context));
    }

}