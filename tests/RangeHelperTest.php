<?php

namespace BillingBoss\Tests;

use BillingBoss\RangeHelper;
use BillingBoss\Exception\RangeException;
use BillingBoss\Exception\RangeOverlapException;
use BillingBoss\Exception\RangeConflictException;
use PHPUnit\Framework\TestCase;

class RangeHelperTest extends TestCase
{

    public function testNoRangeSpecified()
    {
        $this->expectException(RangeException::class);
        RangeHelper::validate('');
        RangeHelper::validate('3%');
    }

    public function testInvalidRangeSpecified()
    {
        $this->expectException(RangeException::class);
        $this->expectExceptionCode(-1);
        RangeHelper::validate('a10 - *');
        RangeHelper::validate('10 - ');
        RangeHelper::validate(' - *');
    }

    public function testThereCanBeOnlyOneOpenEndedUpperLimit()
    {
        $this->expectException(RangeConflictException::class);
        $this->assertEquals([], RangeHelper::validate('1 - * | 1000 - *'));
    }

    public function testInvalidRangeBoundaries()
    {
        $this->expectException(RangeConflictException::class);
        $this->expectExceptionCode(RangeException::LOWER_LIMIT_GREATER_THAN_UPPER_LIMIT);

        $this->assertEquals([2], RangeHelper::validate('1 - 10 | 100 - 50'));
    }

    public function testOverlappingRanges()
    {
        $this->expectException(RangeOverlapException::class);

        RangeHelper::validate('1 - 10 | 5 - 50');

        RangeHelper::validate('1 - 10 | 11 - 20 | 15 - 30');
    }

    public function testValidRanges()
    {
        $this->assertEquals(
            [[1, 10], [11, 50]],
            RangeHelper::validate('1 - 10 | 11 - 50'),
            'No overlapping ranges'
        );

        $this->assertEquals(
            [[1, 10], [11, 50], [51, '*']],
            RangeHelper::validate('1 - 10 | 11 - 50 | 51 - *'),
            'No overlapping ranges'
        );
    }

}
