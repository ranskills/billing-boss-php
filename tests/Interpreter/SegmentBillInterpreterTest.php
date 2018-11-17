<?php

namespace BillingBoss\Tests\Interpreter;

use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;
use BillingBoss\Interpreter\SegmentBillInterpreter;
use PHPUnit\Framework\TestCase;

class SegmentBillInterpreterTest extends TestCase
{

    /**
     * @var BillInterpreter
     */
    private $interpreter;

    public function setUp()
    {
        $this->interpreter = new SegmentBillInterpreter();
    }

    public function testShouldPassWithValidBillingStructure()
    {
        // $context = new BillContext(0, '1, 1 - 100');
        $context = new BillContext(0, '1, 1 - 100 | 5, 101 - *');
        $this->assertTrue($this->interpreter->isValid($context));

        // todo consider handling this 1, 1 - 100 | 5, 101 - 1000| 5, 101 - 1000
        $context = new BillContext(0, '1, 1    -   100 | 5, 101    -   1000| 6, 80     - *');
        $this->assertTrue($this->interpreter->isValid($context));

        // $this->assertTrue($this->interpreter->isValid($context->setStructure('1%, 50    > 5%, 250> 10%, *    ')));

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