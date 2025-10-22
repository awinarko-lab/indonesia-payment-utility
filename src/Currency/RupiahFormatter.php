<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\Currency;

/**
 * Formats numbers as Indonesian Rupiah currency.
 *
 * Provides methods to format numeric values in various Indonesian Rupiah formats,
 * including standard notation (Rp 1.000.000,00) and compact notation (1M, 1B).
 */
final class RupiahFormatter
{
    /**
     * Format a number as Indonesian Rupiah currency.
     *
     * Examples:
     * - format(1000) -> "Rp 1.000,00"
     * - format(1000, false) -> "1.000,00"
     * - format(-1000) -> "(Rp 1.000,00)"
     * - format(1000.50) -> "Rp 1.000,50"
     *
     * @param int|float $amount The amount to format
     * @param bool $withSymbol Whether to include the Rp symbol (default: true)
     * @param bool $withDecimals Whether to include decimal places (default: true)
     * @return string The formatted currency string
     */
    public static function format(int|float $amount, bool $withSymbol = true, bool $withDecimals = true): string
    {
        $isNegative = $amount < 0;
        $absoluteAmount = abs($amount);

        // Format with thousands separator (.) and decimal separator (,)
        $decimals = $withDecimals ? 2 : 0;
        $formatted = number_format($absoluteAmount, $decimals, ',', '.');

        // Add symbol if requested
        if ($withSymbol) {
            $formatted = 'Rp '.$formatted;
        }

        // Handle negative values with parentheses
        if ($isNegative) {
            $formatted = '('.$formatted.')';
        }

        return $formatted;
    }

    /**
     * Format a number in compact notation (K, M, B, T).
     *
     * Examples:
     * - formatCompact(1500) -> "1,5K"
     * - formatCompact(1500000) -> "1,5M"
     * - formatCompact(1500000000) -> "1,5B"
     * - formatCompact(1500000000000) -> "1,5T"
     *
     * @param int|float $amount The amount to format
     * @param bool $withSymbol Whether to include the Rp symbol (default: true)
     * @return string The formatted compact currency string
     */
    public static function formatCompact(int|float $amount, bool $withSymbol = true): string
    {
        $isNegative = $amount < 0;
        $absoluteAmount = abs($amount);

        // Determine the scale
        $suffix = '';
        $divisor = 1;

        if ($absoluteAmount >= 1_000_000_000_000) {
            $suffix = 'T'; // Triliun (Trillion)
            $divisor = 1_000_000_000_000;
        } elseif ($absoluteAmount >= 1_000_000_000) {
            $suffix = 'B'; // Miliar (Billion)
            $divisor = 1_000_000_000;
        } elseif ($absoluteAmount >= 1_000_000) {
            $suffix = 'M'; // Juta (Million)
            $divisor = 1_000_000;
        } elseif ($absoluteAmount >= 1_000) {
            $suffix = 'K'; // Ribu (Thousand)
            $divisor = 1_000;
        }

        $scaled = $absoluteAmount / $divisor;

        // Format with up to 1 decimal place, remove trailing zeros
        $formatted = rtrim(rtrim(number_format($scaled, 1, ',', '.'), '0'), ',');
        $formatted .= $suffix;

        // Add symbol if requested
        if ($withSymbol && $suffix !== '') {
            $formatted = 'Rp '.$formatted;
        } elseif ($withSymbol && $suffix === '') {
            // For amounts less than 1000, use standard format
            return self::format($amount, true, false);
        }

        // Handle negative values
        if ($isNegative) {
            $formatted = '('.$formatted.')';
        }

        return $formatted;
    }
}
