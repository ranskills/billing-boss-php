<?php

namespace BillingBoss\Interpreter;

use BillingBoss\BillContext;
use BillingBoss\AbstractBillInterpreter;

final class PercentageBillInterpreter extends AbstractBillInterpreter
{

    public function __construct()
    {
        parent::__construct('/^\s*(-?\d*\.?\d+)\s*%$/');
    }

    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) {
            return 0.0;
        }

        $percent = $this->matches[1] / 100.00;
        return $context->getAmount() * $percent;
    }
}
