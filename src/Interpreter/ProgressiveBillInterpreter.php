<?php

namespace BillingBoss\Interpreter;

use BillingBoss\BillContext;
use BillingBoss\AbstractBillInterpreter;

final class ProgressiveBillInterpreter extends AbstractBillInterpreter
{

    public function __construct()
    {
        parent::__construct('/^(-?\d*\.?\d+)%,\s*(\d*\.?\d+)(\s*>\s*(-?\d*\.?\d+)%,\s*(\d*\.?\d+))*(\s*>\s*(-?\d*\.?\d+)%,\s*(\*))$/');
    }

    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) {
            return 0.0;
        }

        $parts = preg_split('/\s*>\s*/', $context->getStructure());
        $billableAmount = $context->getAmount();
        $percentageBillInterpreter = new PercentageBillInterpreter();
        $percentageCtxt = new BillContext(0, '0%');
        $bill = 0;

        foreach ($parts as $part) {
            if ($billableAmount === 0.0) {
                break;
            }
            $matches = [];

            \preg_match('/^((-?\d*\.?\d+)%),\s*(\d*\.?\d+|\*)$/', $part, $matches);
            print_r($matches);
            $amount = $matches[3];
            
            if ($amount === '*') {
                $percentageCtxt->setAmount(floatval($billableAmount))
                               ->setStructure($matches[1] . ', 1 - * ');
                $bill += $percentageBillInterpreter->interpret($percentageCtxt);
            } else {
                $billableAmount -= $amount;
                $percentageCtxt->setAmount($amount)
                               ->setStructure($matches[1] . ', 1 - * ');
                $bill += $percentageBillInterpreter->interpret($percentageCtxt);
            }
        }

        return $bill;
    }
}
