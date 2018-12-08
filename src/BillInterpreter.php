<?php
namespace BillingBoss;

use BillingBoss\Exception\Exception;

/**
 * An interface to be implemented by all billing interpreters.
 *
 * @package BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
interface BillInterpreter
{

    /**
     * Interprets the given context and returns the result
     *
     * Test {@link \BillingBoss\BillContext}
     *
     * @param BillContext $context The context to be interpreted
     * @return float The result of the interpretation
     * @throws Exception
     */
    public function interpret(BillContext $context): float;

    /**
     * Validates the BillingContext#structure
     *
     * An interpreter that returns false if it can not match the structure of the context.
     * True is returned if the structure is syntactically correct and contextually it can be interpreted
     *
     * An exception is throw when structurally the notation/structure provided is semantically valid but contextually
     * cannot be interpreted
     *
     * @param BillContext $context The context to be validated
     * @return bool true if the context is validated, false otherwise
     * @throws Exception
     */
    public function isValid(BillContext $context): bool;
}
