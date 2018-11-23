<?php

namespace BillingBoss\Tests\Interpreter;

use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;
use BillingBoss\Interpreter\CappedBillInterpreter;
use PHPUnit\Framework\TestCase;

class CappedBillInterpreterTest extends TestCase
{

    /**
     * @var BillInterpreter
     */
    private $interpreter;

    public function setUp()
    {
        $this->interpreter = new CappedBillInterpreter();
    }

    public function testShouldPassWithValidBillingStructure()
    {
        $context = new BillContext(0, '1% [5, 100], 1 - *');
        $this->assertTrue($this->interpreter->isValid($context));

        $context = new BillContext(0, '1% [5, 100], 1 - 1000 | 2% [10, 200], 1001 - *');
        $this->assertTrue($this->interpreter->isValid($context));

        $this->assertEquals(5.0, $this->interpreter->interpret($context->setAmount(50)));
        $this->assertEquals(10.0, $this->interpreter->interpret($context->setAmount(1000)));
        $this->assertEquals(80.0, $this->interpreter->interpret($context->setAmount(4000)));
        $this->assertEquals(200.0, $this->interpreter->interpret($context->setAmount(1000000)));

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