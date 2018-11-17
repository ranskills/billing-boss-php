<?php

namespace BillingBoss\Interpreter;


use BillingBoss\Expr;
use BillingBoss\BillContext;
use BillingBoss\AbstractBillInterpreter;

final class SegmentBillInterpreter extends AbstractBillInterpreter
{
    const EXP_SINGLE_SEGMENT =  Expr::EXP_NUMBER_OR_PERCENT.','. Expr::EXP_SPACES. Expr::EXP_POSITIVE_NUMBER. Expr::EXP_HYPHEN. Expr::EXP_POSITIVE_NUMBER;

    public function __construct()
    {
        parent::__construct(sprintf('/^%1$s\s*(\|\s*%1$s)*\s*(\|\s*(%2$s),\s*(%3$s)\s*-\s*\*)$/', 
            self::EXP_SINGLE_SEGMENT, 
            Expr::EXP_NUMBER, 
            Expr::EXP_POSITIVE_NUMBER
        ));
    }

    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) return 0.0;

        return 0.0;
    }
}