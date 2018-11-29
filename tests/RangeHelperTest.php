<?php
namespace BillingBoss\Tests;

use BillingBoss\RangeHelper;
use PHPUnit\Framework\TestCase;

class RangeHelperTest extends TestCase
{

    public function testNoRangeSpecified()
    {
        $this->assertEquals([RangeHelper::VALIDATION_NO_RANGE_FOUND, []], RangeHelper::validate(''));
        $this->assertEquals([RangeHelper::VALIDATION_NO_RANGE_FOUND, []], RangeHelper::validate('3%'));
    }

    public function testThereCanBeOnlyOneOpenEndedUpperLimit()
    {
        $this->assertEquals([RangeHelper::VALIDATION_CONFLICT, []], RangeHelper::validate('1 - * | 1000 - *'));
    }

    public function testARangeBoundaries()
    {
        $this->assertEquals([RangeHelper::VALIDATION_CONFLICT, []], RangeHelper::validate('5 - 1'));
        $this->assertEquals([RangeHelper::VALIDATION_CONFLICT, []], RangeHelper::validate('1 - 10 | 100 - 50'));
    }

    public function testOverlappingRanges()
    {
        $this->assertEquals(
            [RangeHelper::VALIDATION_OK, [[1, 10], [11, 50]]], 
            RangeHelper::validate('1 - 10 | 11 - 50'), 
            'No overlapping ranges'
        );

        $this->assertEquals(
            [RangeHelper::VALIDATION_OK, [[1, 10], [11, 50], [51, '*']]], 
            RangeHelper::validate('1 - 10 | 11 - 50 | 51 - *'),
            'No overlapping ranges'
        );

        $this->assertEquals(
            [RangeHelper::VALIDATION_OVERLAPPING_VALUES, []], 
            RangeHelper::validate('1 - 10 | 5 - 50'), 
            'Overlapping ranges'
        );

        $this->assertEquals(
            [RangeHelper::VALIDATION_OVERLAPPING_VALUES, []], 
            RangeHelper::validate('1 - 10 | 11 - 20 | 15 - 30'), 
            'Overlapping ranges'
        );
    }

}
