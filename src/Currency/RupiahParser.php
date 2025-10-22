<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\Currency;

use InvalidArgumentException;

/**
 * Parses Indonesian Rupiah currency strings to numeric values.
 *
 * Supports various input formats including:
 * - Rp 1.000,00 (standard Indonesian format)
 * - 1.000,00 (without symbol)
 * - 1000 (plain number)
 * - 1,000.00 (international format)
 */
final class RupiahParser
{
    /**
     * Parse a Rupiah string to a float value.
     *
     * Examples:
     * - parse("Rp 1.000,00") -> 1000.00
     * - parse("1.000,50") -> 1000.50
     * - parse("1000") -> 1000.00
     * - parse("(Rp 1.000,00)") -> -1000.00
     *
     * @param string $rupiah The Rupiah string to parse
     * @return float The parsed numeric value
     * @throws InvalidArgumentException If the format is invalid
     */
    public static function parse(string $rupiah): float
    {
        $cleaned = self::clean($rupiah);

        if ($cleaned === '') {
            throw new InvalidArgumentException('Cannot parse empty string');
        }

        // Check for negative (parentheses notation)
        $isNegative = false;
        if (str_starts_with($cleaned, '(') && str_ends_with($cleaned, ')')) {
            $isNegative = true;
            $cleaned = trim($cleaned, '()');
        }

        // Check for negative (minus sign)
        if (str_starts_with($cleaned, '-')) {
            $isNegative = true;
            $cleaned = ltrim($cleaned, '-');
        }

        // Remove Rp symbol if present
        $cleaned = trim(str_replace('Rp', '', $cleaned));

        if ($cleaned === '') {
            throw new InvalidArgumentException('Invalid format: no numeric value found');
        }

        // Detect format and parse
        $value = self::parseNumericString($cleaned);

        return $isNegative ? -$value : $value;
    }

    /**
     * Parse a Rupiah string to an integer value.
     *
     * Examples:
     * - parseToInt("Rp 1.000,00") -> 1000
     * - parseToInt("1.234,50") -> 1235 (rounded)
     *
     * @param string $rupiah The Rupiah string to parse
     * @return int The parsed integer value (rounded)
     * @throws InvalidArgumentException If the format is invalid
     */
    public static function parseToInt(string $rupiah): int
    {
        return (int) round(self::parse($rupiah));
    }

    /**
     * Clean the input string.
     *
     * @param string $input The input string
     * @return string The cleaned string
     */
    private static function clean(string $input): string
    {
        return trim($input);
    }

    /**
     * Parse a numeric string in various formats.
     *
     * @param string $numeric The numeric string
     * @return float The parsed value
     * @throws InvalidArgumentException If the format is invalid
     */
    private static function parseNumericString(string $numeric): float
    {
        // Remove all spaces
        $numeric = str_replace(' ', '', $numeric);

        if ($numeric === '') {
            throw new InvalidArgumentException('Invalid format: empty numeric value');
        }

        // Determine format based on separators
        $hasComma = str_contains($numeric, ',');
        $hasDot = str_contains($numeric, '.');

        if (! $hasComma && ! $hasDot) {
            // Plain number: "1000"
            if (! is_numeric($numeric)) {
                throw new InvalidArgumentException("Invalid format: '{$numeric}' is not a valid number");
            }

            return (float) $numeric;
        }

        if ($hasComma && ! $hasDot) {
            // Comma only: could be "1000,50" (Indonesian) or invalid
            return self::parseIndonesianFormat($numeric);
        }

        if ($hasDot && ! $hasComma) {
            // Dot only: could be "1.000" (thousands) or "1000.50" (international decimal)
            return self::parseWithDotOnly($numeric);
        }

        // Both comma and dot present
        return self::parseWithBothSeparators($numeric);
    }

    /**
     * Parse Indonesian format (comma as decimal separator).
     *
     * @param string $numeric The numeric string
     * @return float The parsed value
     */
    private static function parseIndonesianFormat(string $numeric): float
    {
        // Replace comma with dot for decimal
        $normalized = str_replace(',', '.', $numeric);

        if (! is_numeric($normalized)) {
            throw new InvalidArgumentException("Invalid format: '{$numeric}'");
        }

        return (float) $normalized;
    }

    /**
     * Parse format with dot only.
     *
     * @param string $numeric The numeric string
     * @return float The parsed value
     */
    private static function parseWithDotOnly(string $numeric): float
    {
        $dotPosition = strrpos($numeric, '.');
        $beforeDot = substr($numeric, 0, $dotPosition);
        $afterDot = substr($numeric, $dotPosition + 1);

        // If after dot has 3+ digits, it's a thousands separator
        // e.g., "1.000" or "1.000.000"
        if (strlen($afterDot) !== 2) {
            // Treat as thousands separator
            $normalized = str_replace('.', '', $numeric);

            if (! is_numeric($normalized)) {
                throw new InvalidArgumentException("Invalid format: '{$numeric}'");
            }

            return (float) $normalized;
        }

        // Otherwise, treat as decimal separator (international format)
        // e.g., "1000.50"
        if (! is_numeric($numeric)) {
            throw new InvalidArgumentException("Invalid format: '{$numeric}'");
        }

        return (float) $numeric;
    }

    /**
     * Parse format with both comma and dot.
     *
     * @param string $numeric The numeric string
     * @return float The parsed value
     */
    private static function parseWithBothSeparators(string $numeric): float
    {
        $commaPosition = strrpos($numeric, ',');
        $dotPosition = strrpos($numeric, '.');

        if ($dotPosition > $commaPosition) {
            // International format: "1,000.50"
            // Remove commas (thousands separator), keep dot (decimal)
            $normalized = str_replace(',', '', $numeric);
        } else {
            // Indonesian format: "1.000,50"
            // Remove dots (thousands separator), replace comma with dot (decimal)
            $normalized = str_replace('.', '', $numeric);
            $normalized = str_replace(',', '.', $normalized);
        }

        if (! is_numeric($normalized)) {
            throw new InvalidArgumentException("Invalid format: '{$numeric}'");
        }

        return (float) $normalized;
    }
}
