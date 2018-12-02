<?php

namespace BillingBoss\Interpreter;

use BillingBoss\AbstractBillInterpreter;
use BillingBoss\BillContext;
use BillingBoss\Expr;

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
            if ($amount >= floatval($rangeStart)) {
                $compute = false;

                if (is_numeric($rangeEnd)) {
                    $compute = $amount <= floatval($rangeEnd);
                } else {
                    $compute = true;
                }

                if ($compute) {
                    $ctxt = new BillContext($amount, $percent . '%' . ', 1 - * ');
                    $bill = (new PercentageBillInterpreter())->interpret($ctxt);

                    if ($bill > $capMax) {
                        $bill = $capMax;
                    }
                    if ($bill < $capMin) {
                        $bill = $capMin;
                    }
                    break;
                }
            }
        }

        return $bill;
    }
}
