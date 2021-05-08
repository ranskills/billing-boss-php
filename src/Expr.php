<?php
declare(strict_types=1);
namespace BillingBoss;

/**
 * Contains regular expression definitions used in the library.
 *
 * @package   BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
final class Expr
{
    const SPACES = '\s*';
    const HYPHEN = self::SPACES . '-' . self::SPACES;
    const COMMA = self::SPACES . ',' . self::SPACES;
    const PIPE = self::SPACES . '\|' . self::SPACES;
    const NUMBER = '((?:[-+]?\d+\.?\d*[e|E]\-?\d+?|[-+]?\d+\.?\d*))';
    const NUMBER_OR_PERCENT = '('. self::NUMBER . '%?)';
    const PERCENT = self::NUMBER . '%';
    const POSITIVE_NUMBER = '((?:[+]?\d+\.?\d*[e|E]\-?\d+?|[+]?\d+\.?\d*))';
    const POSITIVE_NUMBER_OR_ASTERISK = '((?:[+]?\d+\.?\d*[e|E]\-?\d+?|[+]?\d+\.?\d*|\*))';
    const RANGE = self::POSITIVE_NUMBER . self::HYPHEN . self::POSITIVE_NUMBER_OR_ASTERISK;
}
