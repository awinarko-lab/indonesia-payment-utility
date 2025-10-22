<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\Terbilang;

/**
 * Converts numbers to Indonesian words (terbilang).
 *
 * This class is particularly useful for invoices and receipts where Indonesian law
 * requires amounts to be written in words. Supports conversion up to quadrillions
 * (999,999,999,999,999) with proper Indonesian grammar.
 */
final class Terbilang
{
    /**
     * Base number words (0-19).
     *
     * @var array<int, string>
     */
    private const ONES = [
        0 => '',
        1 => 'satu',
        2 => 'dua',
        3 => 'tiga',
        4 => 'empat',
        5 => 'lima',
        6 => 'enam',
        7 => 'tujuh',
        8 => 'delapan',
        9 => 'sembilan',
        10 => 'sepuluh',
        11 => 'sebelas',
        12 => 'dua belas',
        13 => 'tiga belas',
        14 => 'empat belas',
        15 => 'lima belas',
        16 => 'enam belas',
        17 => 'tujuh belas',
        18 => 'delapan belas',
        19 => 'sembilan belas',
    ];

    /**
     * Convert a number to Indonesian words.
     *
     * Examples:
     * - convert(0) -> "nol"
     * - convert(1000) -> "seribu"
     * - convert(1500) -> "seribu lima ratus"
     * - convert(1234567) -> "satu juta dua ratus tiga puluh empat ribu lima ratus enam puluh tujuh"
     * - convert(1500.25) -> "seribu lima ratus koma dua puluh lima"
     *
     * @param int|float $number The number to convert (up to 999,999,999,999,999)
     * @return string The number in Indonesian words
     */
    public static function convert(int|float $number): string
    {
        // Handle negative numbers
        if ($number < 0) {
            return 'minus '.self::convert(abs($number));
        }

        // Handle zero
        if ($number == 0) {
            return 'nol';
        }

        // Handle decimals
        if (is_float($number) && floor($number) !== $number) {
            $integerPart = (int) floor($number);
            $decimalPart = round(($number - $integerPart) * 100);

            return self::convert($integerPart).' koma '.self::convert((int) $decimalPart);
        }

        return self::convertInteger((int) $number);
    }

    /**
     * Convert a number to Indonesian words with "rupiah" suffix.
     *
     * Examples:
     * - convertRupiah(1000) -> "seribu rupiah"
     * - convertRupiah(1500000) -> "satu juta lima ratus ribu rupiah"
     *
     * @param int|float $amount The amount to convert
     * @return string The amount in Indonesian words with "rupiah" suffix
     */
    public static function convertRupiah(int|float $amount): string
    {
        return self::convert($amount).' rupiah';
    }

    /**
     * Convert an integer to Indonesian words.
     *
     * @param int $number The integer to convert
     * @return string The number in Indonesian words
     */
    private static function convertInteger(int $number): string
    {
        if ($number < 20) {
            return self::ONES[$number];
        }

        if ($number < 100) {
            return self::convertTens($number);
        }

        if ($number < 1000) {
            return self::convertHundreds($number);
        }

        if ($number < 1000000) {
            return self::convertThousands($number);
        }

        if ($number < 1000000000) {
            return self::convertMillions($number);
        }

        if ($number < 1000000000000) {
            return self::convertBillions($number);
        }

        return self::convertTrillions($number);
    }

    /**
     * Convert tens (20-99).
     *
     * @param int $number The number to convert
     * @return string The number in words
     */
    private static function convertTens(int $number): string
    {
        $tens = (int) floor($number / 10);
        $ones = $number % 10;

        $result = self::ONES[$tens].' puluh';

        if ($ones > 0) {
            $result .= ' '.self::ONES[$ones];
        }

        return $result;
    }

    /**
     * Convert hundreds (100-999).
     *
     * @param int $number The number to convert
     * @return string The number in words
     */
    private static function convertHundreds(int $number): string
    {
        $hundreds = (int) floor($number / 100);
        $remainder = $number % 100;

        // Special case: 100 = "seratus" not "satu ratus"
        if ($hundreds === 1) {
            $result = 'seratus';
        } else {
            $result = self::ONES[$hundreds].' ratus';
        }

        if ($remainder > 0) {
            $result .= ' '.self::convertInteger($remainder);
        }

        return $result;
    }

    /**
     * Convert thousands (1,000-999,999).
     *
     * @param int $number The number to convert
     * @return string The number in words
     */
    private static function convertThousands(int $number): string
    {
        $thousands = (int) floor($number / 1000);
        $remainder = $number % 1000;

        // Special case: 1000 = "seribu" not "satu ribu"
        if ($thousands === 1) {
            $result = 'seribu';
        } else {
            $result = self::convertInteger($thousands).' ribu';
        }

        if ($remainder > 0) {
            $result .= ' '.self::convertInteger($remainder);
        }

        return $result;
    }

    /**
     * Convert millions (1,000,000-999,999,999).
     *
     * @param int $number The number to convert
     * @return string The number in words
     */
    private static function convertMillions(int $number): string
    {
        $millions = (int) floor($number / 1000000);
        $remainder = $number % 1000000;

        $result = self::convertInteger($millions).' juta';

        if ($remainder > 0) {
            $result .= ' '.self::convertInteger($remainder);
        }

        return $result;
    }

    /**
     * Convert billions (1,000,000,000-999,999,999,999).
     *
     * @param int $number The number to convert
     * @return string The number in words
     */
    private static function convertBillions(int $number): string
    {
        $billions = (int) floor($number / 1000000000);
        $remainder = $number % 1000000000;

        $result = self::convertInteger($billions).' miliar';

        if ($remainder > 0) {
            $result .= ' '.self::convertInteger($remainder);
        }

        return $result;
    }

    /**
     * Convert trillions (1,000,000,000,000+).
     *
     * @param int $number The number to convert
     * @return string The number in words
     */
    private static function convertTrillions(int $number): string
    {
        $trillions = (int) floor($number / 1000000000000);
        $remainder = $number % 1000000000000;

        $result = self::convertInteger($trillions).' triliun';

        if ($remainder > 0) {
            $result .= ' '.self::convertInteger($remainder);
        }

        return $result;
    }
}
