<?php
declare(strict_types=1);
namespace BillingBoss;

final class Expr
{
    const SPACES = '\s*';
    const HYPHEN = self::SPACES . '-' . self::SPACES;
    const COMMA = self::SPACES . ',' . self::SPACES;
    const PIPE = self::SPACES . '\|' . self::SPACES;
    const NUMBER = '(-?\d*\.?\d+)';
    const NUMBER_OR_PERCENT = '('. self::NUMBER . '%?)'; //'((-?\d*\.?\d+)%?)';
    const PERCENT = self::NUMBER . '%';
    const POSITIVE_NUMBER = '(\d*\.?\d+)';
    const POSITIVE_NUMBER_OR_ASTERISK = '(\d*\.?\d+|\*)';
    const RANGE = self::POSITIVE_NUMBER . self::HYPHEN . self::POSITIVE_NUMBER_OR_ASTERISK;
}
