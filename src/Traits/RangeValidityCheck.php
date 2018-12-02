<?php
/**
 * Billing Boss
 *
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0
 */

namespace BillingBoss\Traits;

use BillingBoss\BillContext;
use BillingBoss\Exception\RangeException;
use BillingBoss\Expr;
use BillingBoss\RangeHelper;

trait RangeValidityCheck
{
    protected $ranges = [];

    /**
     * @param BillContext $context
     * @return bool
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