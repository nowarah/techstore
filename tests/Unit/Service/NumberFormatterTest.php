<?php

namespace App\Tests\Unit\Service;

use App\Service\NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatterTest extends TestCase
{
    public function testFormatBasicAmount(): void
    {
        $this->assertSame('€10.00', NumberFormatter::format(1000));
    }

    public function testFormatZero(): void
    {
        $this->assertSame('€0.00', NumberFormatter::format(0));
    }

    public function testFormatSmallAmount(): void
    {
        $this->assertSame('€0.99', NumberFormatter::format(99));
    }

    public function testFormatLargeAmount(): void
    {
        $this->assertSame('€1,999.00', NumberFormatter::format(199900));
    }
}
