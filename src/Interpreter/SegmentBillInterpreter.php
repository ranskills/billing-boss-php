<?php
/**
 * Billing Boss
 *
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0
 */
namespace BillingBoss\Interpreter;

use BillingBoss\Expr;
use BillingBoss\BillContext;
use BillingBoss\AbstractBillInterpreter;

final class SegmentBillInterpreter extends AbstractBillInterpreter
{
    const EXP_SINGLE_SEGMENT =  Expr::NUMBER_OR_PERCENT .
                                ',' .
                                Expr::SPACES .
                                Expr::POSITIVE_NUMBER .
                                Expr::HYPHEN .
                                Expr::POSITIVE_NUMBER;

    public function __construct()
    {
        parent::__construct(sprintf(
            '/^%1$s\s*(\|\s*%1$s)*\s*(\|\s*(%2$s),\s*(%3$s)\s*-\s*\*)$/',
            self::EXP_SINGLE_SEGMENT,
            Expr::NUMBER_OR_PERCENT,
            Expr::POSITIVE_NUMBER
        ));
    }

    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) {
            return 0.0;
        }

        $bill = 0.0;
        $parts = preg_split('/\s*\|\s*/', $context->getStructure());

        foreach ($parts as $part) {
            $matches = [];
            $regex = Expr::NUMBER_OR_PERCENT .
                    ',' .
                    Expr::SPACES .
                    Expr::POSITIVE_NUMBER .
                    Expr::HYPHEN .
                    Expr::POSITIVE_NUMBER_OR_ASTERISK;

            \preg_match(sprintf('/^%s$/', $regex), $part, $matches);

            $min = \floatval($matches[3]);
            $max = $matches[4];
            $amount = $context->getAmount();

            $compute = $max === '*' ? $min <= $amount : $min <= $amount && $amount <= floatval($max);

            if ($compute) {
                $ctxt = new BillContext($amount, $matches[1]);
                $percentBillInterpreter = new PercentageBillInterpreter();
                if ($percentBillInterpreter->isValid($ctxt)) {
                    $bill = $percentBillInterpreter->interpret($ctxt);
                } else {
                    $bill = (new FlatRateBillInterpreter())->interpret($ctxt);
                }
                break;
            }
        }

        return $bill;
    }
}
