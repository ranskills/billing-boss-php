<?php
/**
 * Billing Boss
 *
 * @copyright 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @link      https://github.com/ranskills/billing-boss-php
 */

namespace BillingBoss\Traits;

use BillingBoss\BillContext;
use BillingBoss\Exception\RangeException;
use BillingBoss\Expr;
use BillingBoss\RangeHelper;

/**
 * A trait to that implements isValid function for range-related interpreters
 *
 * @package BillingBoss
 * @author  Ransford Ako Okpoti <ranskills@yahoo.co.uk>
 * @since   1.0.0
 */
trait RangeValidityCheck
{
    protected $ranges = [];

    /**
     * Validates the BillingContext#structure
     *
     * An interpreter that returns false if it can not match the structure of the
     * context.
     * True is returned if the structure is syntactically correct and contextually
     * it can be interpreted
     *
     * An exception is throw when structurally the notation/structure provided is
     * semantically valid but contextually cannot be interpreted
     *
     * @param BillContext $context The context to be validated
     * @return bool true if the context is validated, false otherwise
     * @throws RangeException
     */
    public function isValid(BillContext $context): bool
    {
        if (preg_match(sprintf('/%s/', Expr::RANGE), $context->getStructure()) === 0) {
            return false;
        }

        $this->ranges = RangeHelper::validate($context->getStructure());

        return count($this->ranges) > 0;
    }
}
