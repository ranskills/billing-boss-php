<?php

namespace BillingBoss\Tests;

use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;
use BillingBoss\Exception\RangeOverlapException;
use BillingBoss\Interpreter\FlatRateBillInterpreter;
use PHPUnit\Framework\TestCase;

class FlatRateBillInterpreterTest extends TestCase
{
    /**
     * @var BillInterpreter
     */
    private $interpreter;

    public function setUp(): void
    {
        $this->interpreter = new FlatRateBillInterpreter();
    }

    public function testShouldPassWithValidBillingStructure()
    {
        $context = new BillContext(0, '5, 1 - *');
        $this->assertTrue($this->interpreter->isValid($context));

        $context = new BillContext(0, '1, 1 - 499     | 10, 5e2 - *');
        $this->assertTrue($this->interpreter->isValid($context));

        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(100));
        $this->assertEquals(1.0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(499));
        $this->assertEquals(1.0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(500));
        $this->assertEquals(10.0, $bill);

        $bill = $this->interpreter->interpret($context->setAmount(-100));
        $this->assertEquals(0, $bill);
    }

    public function testOverlappingRangesShouldThrowAnException()
    {
        $this->expectException(RangeOverlapException::class);

        $context = new BillContext(50, '1, 1 - 100 | 2.5, 70 - 500');
        $bill = $this->interpreter->interpret($context);
        $this->assertEquals(0.0, $bill, 'Overlapping ranges should ALWAYS return a bill of zero');
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