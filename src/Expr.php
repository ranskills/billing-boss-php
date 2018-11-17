<?php
declare(strict_types=1);
namespace BillingBoss;

final class Expr
{
    const EXP_SPACES = '\s*';
    const EXP_HYPHEN = self::EXP_SPACES . '-' . self::EXP_SPACES;
    const EXP_NUMBER = '(-?\d*\.?\d+)';
    const EXP_NUMBER_OR_PERCENT = '('. self::EXP_NUMBER . '%?)'; //'((-?\d*\.?\d+)%?)';
    const EXP_POSITIVE_NUMBER = '(\d*\.?\d+)';
}
