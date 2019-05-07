<?php

namespace BillingBoss\Interpreter;

use BillingBoss\AbstractBillInterpreter;
use BillingBoss\BillContext;
use BillingBoss\Expr;
use BillingBoss\RangeHelper;

/**
 * Capped bill interpreter
 *
 * @package   BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
final class CappedBillInterpreter extends AbstractBillInterpreter
{
    private const A =
        Expr::PERCENT .
        Expr::SPACES .
        '\[' .
        Expr::SPACES .
        Expr::POSITIVE_NUMBER .
        Expr::COMMA .
        Expr::POSITIVE_NUMBER .
        Expr::SPACES .
        '\]' .
        Expr::COMMA .
        Expr::RANGE;

    public function __construct()
    {
        parent::__construct(sprintf('/^%1$s(%2$s%1$s)*$/', self::A, Expr::PIPE));
    }

    private function enforceBoundaries($bill, $lowerLimit, $upperLimit)
    {
        if ($bill > $upperLimit) {
            $bill = $upperLimit;
        }
        if ($bill < $lowerLimit) {
            $bill = $lowerLimit;
        }

        return $bill;
    }

    public function interpret(BillContext $context): float
    {
        $bill = 0.0;

        if (!$this->isValid($context)) {
            return 0.0;
        }

        $parts = preg_split(sprintf('/%s/', Expr::PIPE), $context->getStructure());

        foreach ($parts as $part) {
            $matches = [];

            \preg_match(sprintf('/^%s$/', self::A), $part, $matches);

            $percent = $matches[1];
            $capMin = $matches[2];
            $capMax = $matches[3];
            $rangeStart = $matches[4];
            $rangeEnd = $matches[5];

            $amount = $context->getAmount();
            if (RangeHelper::isInRange([$rangeStart, $rangeEnd], $amount)) {
                $ctxt = new BillContext($amount, $percent . '%' . ', 1 - * ');
                $bill = (new PercentageBillInterpreter())->interpret($ctxt);

                $bill = $this->enforceBoundaries($bill, $capMin, $capMax);
                break;
            }
        }

        return $bill;
    }
}
