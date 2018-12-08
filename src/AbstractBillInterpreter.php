<?php

namespace BillingBoss;

use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;

/**
 * An abstract class that implements BillInterpreter
 *
 * @package BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
abstract class AbstractBillInterpreter implements BillInterpreter
{
    protected $regex;
    protected $matches = [];

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }


    abstract public function interpret(BillContext $context): float;

    public function isValid(BillContext $context): bool
    {
        return preg_match($this->regex, $context->getStructure(), $this->matches) === 1;
    }
}
