<?php
declare(strict_types=1);
namespace BillingBoss;

final class Expr
{
    const SPACES = '\s*';
    const HYPHEN = self::SPACES . '-' . self::SPACES;
    const NUMBER = '(-?\d*\.?\d+)';
    const NUMBER_OR_PERCENT = '('. self::NUMBER . '%?)'; //'((-?\d*\.?\d+)%?)';
    const POSITIVE_NUMBER = '(\d*\.?\d+)';
    const POSITIVE_NUMBER_OR_ASTERISK = '(\d*\.?\d+|\*)';
}
