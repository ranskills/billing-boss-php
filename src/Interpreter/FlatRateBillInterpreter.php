<?php
/**
 * Created by PhpStorm.
 * User: ranskills
 * Date: 16/11/18
 * Time: 11:37
 */

namespace BillingBoss\Interpreter;

use BillingBoss\BillContext;
use BillingBoss\AbstractBillInterpreter;

final class FlatRateBillInterpreter extends AbstractBillInterpreter
{

    public function __construct()
    {
        parent::__construct('/^\s*(-?\d*\.?\d+)\s*$/');
    }

    public function interpret(BillContext $context): float
    {
        if (!$this->isValid($context)) return 0;

        return $this->matches[0];
    }
}