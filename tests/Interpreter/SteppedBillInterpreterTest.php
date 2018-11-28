<?php

namespace BillingBoss\Tests\Interpreter;

use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;
use BillingBoss\Interpreter\SteppedBillInterpreter;
use PHPUnit\Framework\TestCase;

class SteppedBillInterpreterTest extends TestCase
{

    /**
     * @var BillInterpreter
     */
    private $interpreter;

    public function setUp()
    {
        $this->interpreter = new SteppedBillInterpreter();
    }

    public function testShouldPassWithValidBillingStructure()
    {
        $context = new BillContext(0, '1, 1+');
        $this->assertTrue($this->interpreter->isValid($context));

        $context = new BillContext(0, '1, 5+');
        $this->assertTrue($this->interpreter->isValid($context));

        $this->assertEquals(0.0, $this->interpreter->interpret($context->setAmount(0)));
        $this->assertEquals(1.0, $this->interpreter->interpret($context->setAmount(5)));
        $this->assertEquals(2.0, $this->interpreter->interpret($context->setAmount(10)));
        $this->assertEquals(3.0, $this->interpreter->interpret($context->setAmount(13)));
        $this->assertEquals(0.0, $this->interpreter->interpret($context->setAmount(-1)));
    }

    public function testShouldNotBeAbleToInterpretInvalidBillingStructure()
    {
        $context = new BillContext(0, '5');
        $this->assertFalse($this->interpreter->isValid($context), 'Flat rate billing');

        $context = new BillContext(0, '1%, 5+');
        $this->assertFalse($this->interpreter->isValid($context));

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