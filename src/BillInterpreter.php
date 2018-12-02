<?php

namespace BillingBoss;

use BillingBoss\Exception\Exception;

interface BillInterpreter
{

    /**
     * @param BillContext $context
     * @return float
     * @throws Exception
     */
    public function interpret(BillContext $context): float;

    /**
     * @param BillContext $context
     * @return bool
     * @throws Exception
     */
    public function isValid(BillContext $context): bool;
}
