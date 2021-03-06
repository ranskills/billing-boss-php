<?php

namespace BillingBoss\Exception;

/**
 * The parent exception from which other range-related exceptions extend
 *
 * @package   BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
class RangeException extends Exception
{
    const NO_RANGE_FOUND = 0;
    const LOWER_LIMIT_GREATER_THAN_UPPER_LIMIT = 1;
    const MULTIPLE_OPEN_ENDED_UPPER_LIMIT = 2;

    protected $ranges = [];

    public function __construct($message, $code = 0, $ranges = [])
    {
        parent::__construct($message, $code);
        $this->ranges = $ranges;
    }
}
