<?php

namespace BillingBoss\Interpreter;

use BillingBoss\Exception\RangeException;
use BillingBoss\Expr;
use BillingBoss\BillContext;
use BillingBoss\AbstractBillInterpreter;
use BillingBoss\RangeHelper;
use BillingBoss\Traits\RangeValidityCheck;

/**
 * Percentage bill interpreter
 *
 * @package   BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
final class PercentageBillInterpreter extends AbstractBillInterpreter
{
    use RangeValidityCheck;

    private const EXPRESSION = Expr::PERCENT .
                               Expr::COMMA .
                               Expr::RANGE;

    public function __construct()
    {
        parent::__construct(sprintf('/^(%1$s)(%2$s%1$s)*$/', self::EXPRESSION, Expr::PIPE));
    }

    /**
     * @param BillContext $context
     * @return float
     * @throws RangeException
     */
    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) {
            return 0.0;
        }
        $bill = 0.0;

        $parts = preg_split(sprintf('/%s/', Expr::PIPE), $context->getStructure());
        $len = $parts === false ? 0 : count($parts);

        for ($i = 0; $i < $len; $i++) {
            $range = $this->ranges[$i];
            $matches = [];

            \preg_match(sprintf('/^%s$/', self::EXPRESSION), $parts[$i], $matches);

            $amount = $context->getAmount();

            if (RangeHelper::isInRange($range, $amount)) {
                $bill = ($matches[1] * $amount) / 100.00;
                break;
            }
        }

        return $bill;
    }
}
