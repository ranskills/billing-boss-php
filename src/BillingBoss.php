<?php

namespace BillingBoss;

use BillingBoss\Interpreter\FlatRateBillInterpreter;
use BillingBoss\Interpreter\PercentageBillInterpreter;
use BillingBoss\Interpreter\CappedBillInterpreter;
use BillingBoss\Interpreter\ProgressiveBillInterpreter;
use BillingBoss\Interpreter\SteppedBillInterpreter;

/**
 * A billing interpreter aggregator
 *
 * @package   BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
final class BillingBoss
{
    /**
     * @var BillInterpreter[]
     */
    private static $interpreters = [];

    public static function getInterpreters()
    {
        if (!empty(self::$interpreters)) {
            return self::$interpreters;
        }

        self::addInterpreter(new CappedBillInterpreter());
        self::addInterpreter(new FlatRateBillInterpreter());
        self::addInterpreter(new PercentageBillInterpreter());
        self::addInterpreter(new ProgressiveBillInterpreter());
        self::addInterpreter(new SteppedBillInterpreter());

        return self::$interpreters;
    }

    public static function addInterpreter(BillInterpreter $interpreter)
    {
        self::$interpreters[get_class($interpreter)] = $interpreter;
    }

    public static function removeInterpreter(BillInterpreter $interpreter)
    {
        $key = get_class($interpreter);
        if (isset(self::$interpreters[$key])) {
            unset(self::$interpreters[$key]);
        }
    }

    /**
     * @param BillContext $context
     * @return float
     * @throws Exception\Exception
     */
    public static function bill(BillContext $context): float
    {
        $interpreters = self::getInterpreters();

        foreach ($interpreters as $interpreter) {
            if ($interpreter->isValid($context)) {
                return $interpreter->interpret($context);
            }
        }

        return 0.0;
    }
}
