<?php

namespace BillingBoss;

use BillingBoss\Interpreter\FlatRateBillInterpreter;
use BillingBoss\Interpreter\PercentageBillInterpreter;

class BillingBoss
{
    /**
     * @var BillInterpreter[]
     */
    private static $interpreters = [];

    public static function getInterpreters()
    {
        if (self::$interpreters) {
            return self::$interpreters;
        }

        self::addInterpreter(new FlatRateBillInterpreter());
        self::addInterpreter(new PercentageBillInterpreter());

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
