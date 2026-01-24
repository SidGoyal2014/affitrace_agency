<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Utils\CurrencyUtils;

class CurrencyTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_currency_conversion(): void
    {
        // 1. Test USD to USD (should be 1:1)
        $val = CurrencyUtils::getNewCurrencyValue(100, 'USD');
        $this->assertEquals(100, $val);

        // 2. Test EUR to USD
        // Rate from array: "EUR" => 0.853
        // Calculation: 100 / 0.853 = 117.233...
        $valEUR = CurrencyUtils::getNewCurrencyValue(100, 'EUR');
        $expected = 100 / 0.853;
        $this->assertEquals($expected, $valEUR);

        // 3. Test Unknown (should return original)
        $valUnknown = CurrencyUtils::getNewCurrencyValue(100, 'XYZ');
        $this->assertEquals(100, $valUnknown);
    }
}
