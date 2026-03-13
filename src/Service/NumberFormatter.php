<?php

namespace App\Service;

class NumberFormatter
{
    public static function format(int $cents): string
    {
        return '€' . number_format($cents / 100, 2);
    }
}
