<?php

namespace BillingBoss\Interpreter;

use BillingBoss\Exception\RangeException;
use BillingBoss\Expr;
use BillingBoss\BillContext;
use BillingBoss\AbstractBillInterpreter;
use BillingBoss\RangeHelper;

final class FlatRateBillInterpreter extends AbstractBillInterpreter
{
    private const EXPRESSION = Expr::POSITIVE_NUMBER .
                               Expr::COMMA .
                               Expr::RANGE;
    private $ranges = [];

    public function __construct()
    {
        parent::__construct(sprintf('/^(%1$s)(%2$s%1$s)*$/', self::EXPRESSION, Expr::PIPE));
    }

    /**
     * @param BillContext $context
     * @return bool
     * @throws RangeException
     */
    public function isValid(BillContext $context): bool
    {
        if (preg_match(sprintf('/%s/', Expr::RANGE), $context->getStructure()) === 0) {
            return false;
        }

        $this->ranges = RangeHelper::validate($context->getStructure());

        return count($this->ranges) > 0;
    }

    /**
     * @param BillContext $context
     * @return float
     * @throws RangeException
     */
    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) {
            return 0;
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
                $bill = $matches[1];
                break;
            }
        }

        return $bill;
    }
}
