<?php
/**
 * Created by PhpStorm.
 * User: ranskills
 * Date: 16/11/18
 * Time: 11:37
 */

namespace BillingBoss\Interpreter;

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

    public function isValid(BillContext $context): bool
    {
        list($result, $this->ranges) = RangeHelper::validate($context->getStructure());
        return $result === RangeHelper::VALIDATION_OK;
    }

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
