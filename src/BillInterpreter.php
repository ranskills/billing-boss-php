<?php

namespace BillingBoss;

interface BillInterpreter
{

    public function interpret(BillContext $context): float;

    public function isValid(BillContext $context): bool;
}
