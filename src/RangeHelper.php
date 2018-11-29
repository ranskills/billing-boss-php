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

final class RangeHelper
{
    const VALIDATION_NO_RANGE_FOUND = 'NO RANGE FOUND';
    const VALIDATION_OK = 'OK';
    //todo change to overlapping range
    const VALIDATION_OVERLAPPING_VALUES = 'OVERLAPPING VALUES';
    const VALIDATION_CONFLICT = 'CONFLICT';

    public static function validate($str)
    {
        $ranges = [];
        $matches = [];

        $numMatches = preg_match_all(sprintf('/%s/', Expr::RANGE), $str, $matches);
        if ($numMatches === 0) {
            return [self::VALIDATION_NO_RANGE_FOUND, []];
        }

        $numAstericks = substr_count($str, '*');
        if ($numAstericks > 1) {
            return [self::VALIDATION_CONFLICT, []];
        }

        $lowerLimits = $matches[1];
        $upperLimits = $matches[2];
        $ranges = [];
        for ($i = 0; $i < count($lowerLimits); $i++) {
            if (is_numeric($upperLimits[$i]) && floatval($lowerLimits[$i]) > floatval($upperLimits[$i])) {
                return [self::VALIDATION_CONFLICT, []];
            }
            $ranges[] = [$lowerLimits[$i], $upperLimits[$i]];
        }

        for ($i = 0; $i < count($ranges); $i++) {
            for ($j = $i + 1; $j < count($ranges); $j++) {
                $inRange = self::isInRange($ranges[$i], $ranges[$j][0]) ||
                self::isInRange($ranges[$i], $ranges[$j][1]) ||

                self::isInRange($ranges[$j], $ranges[$i][0]) ||
                self::isInRange($ranges[$j], $ranges[$i][1]);

                if ($inRange) {
                    return [self::VALIDATION_OVERLAPPING_VALUES, []];
                }
            }
        }

        return [self::VALIDATION_OK, $ranges];
    }

    public static function getRangeLimits($str)
    {
        $ranges = [];

        $numMatches = preg_match_all(sprintf('/%s/', Expr::RANGE), $str, $matches);
        if ($numMatches === 0) {
            return $ranges;
        }

        $lowerLimits = $matches[1];
        $upperLimits = $matches[2];
        for ($i = 0; $i < count($lowerLimits); $i++) {
            if (is_numeric($upperLimits[$i]) && floatval($lowerLimits[$i]) > floatval($upperLimits[$i])) {
                return self::VALIDATION_CONFLICT;
            }
            $ranges[] = [$lowerLimits[$i], $upperLimits[$i]];
        }

        return $ranges;
    }

    public static function isInRange(array $range, $value)
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
