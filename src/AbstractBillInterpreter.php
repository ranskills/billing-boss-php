<?php

namespace BillingBoss;


use BillingBoss\BillContext;
use BillingBoss\BillInterpreter;

abstract class AbstractBillInterpreter implements BillInterpreter
{
    protected $regex;
    protected $matches = [];

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }


    public abstract function interpret(BillContext $context): float;

    function isValid(BillContext $context): bool
    {
        return preg_match($this->regex, $context->getStructure(), $this->matches);
    }
}