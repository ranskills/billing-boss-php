<?php
/**
 * Billing Boss
 *
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0
 */

namespace BillingBoss\Exception;

/**
 * Range overlap exception
 *
 * Exception is throw when ranges overlap. E.g.
 *
 * ``` 0 - 100 | 30 - 200 ```
 * ```echo $name;```
 * ```php
 *  echo $name;
 * ```
 * > Trying
 *
 * @package   BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
class RangeOverlapException extends RangeException
{

}
