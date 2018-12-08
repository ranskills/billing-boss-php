<?php
/**
 * Billing Boss
 *
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0
 */

namespace BillingBoss;

use BillingBoss\Exception\RangeException;
use BillingBoss\Exception\RangeOverlapException;
use BillingBoss\Exception\RangeConflictException;

/**
 * A utility class for range notation related functions
 *
 * @package   BillingBoss
 * @link      https://github.com/ranskills/billing-boss-php
 * @copyright Copyright (c) 2018 Ransford Ako Okpoti
 * @license   Refer to the LICENSE distributed with this library
 * @since     1.0.0
 */
final class RangeHelper
{

    /**
     * @param $str
     * @return array
     * @throws RangeException
     */
    public static function validate($str): array
    {
        $matches = [];
        $numMatches = preg_match_all(sprintf('/%s/', Expr::RANGE), $str, $matches);
        if ($numMatches === 0) {
            throw new RangeConflictException('No range values provided', RangeException::NO_RANGE_FOUND);
        }

        $numAstericks = substr_count($str, '*');
        if ($numAstericks > 1) {
            throw new RangeConflictException(
                'More than one * provided in the ranges. There can be only one within a set of ranges to be examined.',
                RangeException::MULTIPLE_OPEN_ENDED_UPPER_LIMIT
            );
        }

        $ranges = self::getRangeLimits($str);
        $overlappingRanges = self::findOverlappingRanges($ranges);

        if (count($overlappingRanges) !== 0) {
            $message = sprintf(
                'The ranges %s - %s and %s - %s are overlapping and will lead to unexpected results.',
                $overlappingRanges[0][0],
                $overlappingRanges[0][1],
                $overlappingRanges[1][0],
                $overlappingRanges[1][1]
            );

            throw new RangeOverlapException(
                $message,
                RangeException::NO_RANGE_FOUND,
                $overlappingRanges
            );
        }

        return $ranges;
    }

    /**
     * Returns a list of overlapping ranges
     *
     * @param array $ranges
     * @return array A non-empty array if there are overlapping ranges, an empty array otherwise
     */
    private static function findOverlappingRanges(array $ranges): array
    {
        $numRanges = count($ranges);

        for ($i = 0; $i < $numRanges; $i++) {
            for ($j = $i + 1; $j < $numRanges; $j++) {
                $inRange = self::isInRange($ranges[$i], $ranges[$j][0]) ||
                           self::isInRange($ranges[$i], $ranges[$j][1]) ||
                           self::isInRange($ranges[$j], $ranges[$i][0]) ||
                           self::isInRange($ranges[$j], $ranges[$i][1]);

                if ($inRange) {
                    return [
                        $ranges[$i],
                        $ranges[$j]
                    ];
                }
            }
        }

        return [];
    }

    /**
     * Returns the ranges expressed in a string.
     *
     * @param string $str
     * @return array
     * @throws RangeConflictException
     */
    private static function getRangeLimits($str): array
    {
        $ranges = [];

        preg_match_all(sprintf('/%s/', Expr::RANGE), $str, $matches);

        $lowerLimits = $matches[1];
        $upperLimits = $matches[2];
        $len = count($lowerLimits);

        for ($i = 0; $i < $len; $i++) {
            if (is_numeric($upperLimits[$i]) && floatval($lowerLimits[$i]) > floatval($upperLimits[$i])) {
                $message = sprintf(
                    'Invalid limits provided. The lower limit (%s) is greater than the upper limit (%s)',
                    $lowerLimits[$i],
                    $upperLimits[$i]
                );
                throw new RangeConflictException(
                    $message,
                    RangeException::LOWER_LIMIT_GREATER_THAN_UPPER_LIMIT
                );
            }

            $ranges[] = [$lowerLimits[$i], $upperLimits[$i]];
        }

        return $ranges;
    }

    /**
     * Checks if a given value is within a specified range
     *
     * @param array $range An array of two elements with the first and last elements being the lower and upper limits
     *                     for the boundary
     * @param              string|integer $value
     *
     * @return bool        true if value is within the range, false otherwise
     */
    public static function isInRange(array $range, $value): bool
    {
        if ($value === '*') {
            return false;
        }

        if (is_numeric($range[1])) {
            return $value >= $range[0] && $value <= $range[1];
        }

        return $value >= $range[0];
    }
}
