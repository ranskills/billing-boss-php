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

final class SteppedBillInterpreter extends AbstractBillInterpreter
{
    private const EXPRESSION =  Expr::POSITIVE_NUMBER .
                                Expr::COMMA .
                                Expr::POSITIVE_NUMBER .
                                '\+';

    public function __construct()
    {
        parent::__construct(sprintf('/^%s$/', self::EXPRESSION));
    }

    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) {
            return 0.0;
        }
        $bill = 0;
        \preg_match(sprintf('/^%s$/', self::EXPRESSION), $context->getStructure(), $matches);

        $amount = $context->getAmount();
        $billPerStep = $matches[1];
        $step = $matches[2];

        while($amount > 0) {
            $bill += $billPerStep;
            $amount -= $step;
        }

        return $bill;
    }
}
