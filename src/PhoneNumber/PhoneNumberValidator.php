<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\PhoneNumber;

/**
 * Validates Indonesian phone numbers.
 *
 * Supports validation of mobile and landline numbers, and can identify
 * the mobile operator from the prefix.
 */
final class PhoneNumberValidator
{
    /**
     * Indonesian mobile operator prefixes.
     *
     * @var array<string, array<string>>
     */
    private const MOBILE_PREFIXES = [
        'Telkomsel' => ['0811', '0812', '0813', '0821', '0822', '0823', '0852', '0853'],
        'Indosat' => ['0814', '0815', '0816', '0855', '0856', '0857', '0858'],
        'XL' => ['0817', '0818', '0819', '0859', '0877', '0878'],
        'Tri' => ['0895', '0896', '0897', '0898', '0899'],
        'Smartfren' => ['0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889'],
    ];

    /**
     * Indonesian landline area codes (major cities).
     *
     * @var array<string>
     */
    private const LANDLINE_PREFIXES = [
        '021',  // Jakarta
        '022',  // Bandung
        '024',  // Semarang
        '031',  // Surabaya
        '061',  // Medan
    ];

    /**
     * Validate an Indonesian phone number.
     *
     * @param string $phone The phone number to validate
     * @return bool True if valid
     */
    public static function validate(string $phone): bool
    {
        $cleaned = self::cleanNumber($phone);

        if ($cleaned === '') {
            return false;
        }

        return self::isMobile($cleaned) || self::isLandline($cleaned);
    }

    /**
     * Check if a phone number is a mobile number.
     *
     * @param string $phone The phone number to check
     * @return bool True if mobile
     */
    public static function isMobile(string $phone): bool
    {
        $cleaned = self::cleanNumber($phone);
        $normalized = self::normalizeToLocal($cleaned);

        if (! str_starts_with($normalized, '08')) {
            return false;
        }

        // Check length (mobile numbers: 10-13 digits starting with 08)
        $length = strlen($normalized);
        if ($length < 10 || $length > 13) {
            return false;
        }

        // Check if prefix matches known operator
        return self::getOperator($phone) !== null;
    }

    /**
     * Check if a phone number is a landline number.
     *
     * @param string $phone The phone number to check
     * @return bool True if landline
     */
    public static function isLandline(string $phone): bool
    {
        $cleaned = self::cleanNumber($phone);
        $normalized = self::normalizeToLocal($cleaned);

        // Landline format: 0xx-xxxx-xxxx (area code + number)
        foreach (self::LANDLINE_PREFIXES as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                $length = strlen($normalized);

                // Landline numbers typically 9-11 digits
                return $length >= 9 && $length <= 11;
            }
        }

        return false;
    }

    /**
     * Get the mobile operator name from the phone number.
     *
     * @param string $phone The phone number
     * @return string|null The operator name, or null if not found/invalid
     */
    public static function getOperator(string $phone): ?string
    {
        $cleaned = self::cleanNumber($phone);
        $normalized = self::normalizeToLocal($cleaned);

        foreach (self::MOBILE_PREFIXES as $operator => $prefixes) {
            foreach ($prefixes as $prefix) {
                if (str_starts_with($normalized, $prefix)) {
                    return $operator;
                }
            }
        }

        return null;
    }

    /**
     * Clean phone number by removing non-numeric characters.
     *
     * @param string $phone The phone number
     * @return string The cleaned phone number
     */
    private static function cleanNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^\d]/', '', trim($phone));

        return $cleaned ?? '';
    }

    /**
     * Normalize phone number to local format (starting with 0).
     *
     * @param string $phone The cleaned phone number
     * @return string The normalized phone number
     */
    private static function normalizeToLocal(string $phone): string
    {
        // Remove country code if present
        if (str_starts_with($phone, '62')) {
            return '0'.substr($phone, 2);
        }

        return $phone;
    }
}
