<?php

namespace BillingBoss\Tests;


use BillingBoss\BillInterpreter;
use BillingBoss\BillContext;
use BillingBoss\BillingBoss;
use BillingBoss\AbstractBillInterpreter;
use BillingBoss\Exception\RangeOverlapException;
use PHPUnit\Framework\TestCase;

class BillingBossTest extends TestCase
{

    public function testDefaultInterpretersAreLoaded()
    {
        $interpreters = BillingBoss::getInterpreters();

        $this->assertTrue(count($interpreters) > 0);
    }

    public function testAddingNewInterpreters()
    {
        $interpreters = BillingBoss::getInterpreters();
        $count = count($interpreters);

        $dummyInterpreter = new class extends AbstractBillInterpreter {
            public function __construct()
            {
                parent::__construct('/^\s*(-?\d*\.?\d+)\s*$/');
            }
        
            public function interpret(BillContext $context): float
            {
                if (!$this->isValid($context)) {
                    return 0;
                }
        
                return 0;
            }
        };

        BillingBoss::addInterpreter($dummyInterpreter);
        $this->assertEquals($count + 1, count(BillingBoss::getInterpreters()));

        BillingBoss::removeInterpreter($dummyInterpreter);
        $this->assertEquals($count, count(BillingBoss::getInterpreters()));
    }

    public function testAnInterpreterHandlesAKnownBillingStructure()
    {
        $context = new BillContext(100, '3, 1 - *');

        $bill = BillingBoss::bill($context);
        $this->assertEquals(3, $bill);
    }


    public function testAnExceptionIsThrownForOverlappingRanges()
    {
        $this->expectException(RangeOverlapException::class);
        
        $context = new BillContext(100, '1, 1 - 100 | 2.5, 70 - 500');

        BillingBoss::bill($context);
    }

    public function testBillingReturnsZeroForUnhandledBillingStructure()
    {
        $context = new BillContext(100, 'No Notation');

        $bill = BillingBoss::bill($context);
        $this->assertEquals(0, $bill);
    }

}