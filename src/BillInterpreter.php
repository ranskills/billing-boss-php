<?php

namespace BillingBoss;


interface BillInterpreter
{

    function interpret(BillContext $context): float;

    function isValid(BillContext $context): bool;
}