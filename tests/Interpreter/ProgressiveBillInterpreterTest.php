<?php

namespace BillingBoss\Tests\Interpreter;

use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;
use BillingBoss\Interpreter\ProgressiveBillInterpreter;
use PHPUnit\Framework\TestCase;

class ProgressiveBillInterpreterTest extends TestCase
{
    const GHANA_INCOME_TAX = '0%, 261 > 5%, 70 > 10%, 100 > 17.5%, 2810 > 25%, *';

    /**
     * @var BillInterpreter
     */
    private $interpreter;

    public function setUp(): void
    {
        $this->interpreter = new ProgressiveBillInterpreter();
    }

    public function testShouldPassWithValidBillingStructure()
    {
        $context = new BillContext(0, self::GHANA_INCOME_TAX);
        $this->assertTrue($this->interpreter->isValid($context));

        $this->assertTrue($this->interpreter->isValid($context->setStructure('1%, 50    > 5%, 250> 10%, *    ')));

    }

    public function testGhanaIncomeTaxRates()
    {
        $context = new BillContext(0, self::GHANA_INCOME_TAX);
        $this->assertTrue($this->interpreter->isValid($context));

        $bill = $this->interpreter->interpret($context->setAmount(331));
        $this->assertEquals(3.5, $bill);

        // $bill = $this->interpreter->interpret($context->setAmount(331));
        $this->assertEquals(13.5, $this->interpreter->interpret($context->setAmount(431)));
        $this->assertEquals(505.25, $this->interpreter->interpret($context->setAmount(3241)));
        $this->assertEquals(945, $this->interpreter->interpret($context->setAmount(5000)));
        $this->assertEquals(2195, $this->interpreter->interpret($context->setAmount(10000)));
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