<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\PhoneNumber;

use InvalidArgumentException;

/**
 * Formats and normalizes Indonesian phone numbers.
 *
 * Supports conversion between different formats:
 * - Local format: 08xx-xxxx-xxxx
 * - International format: +62 8xx-xxxx-xxxx
 * - E.164 format: 628xxxxxxxxxx
 */
final class PhoneNumberFormatter
{
    /**
     * Convert to international format (+62 8xx-xxxx-xxxx).
     *
     * Examples:
     * - toInternational("08123456789") -> "+62 812-3456-789"
     * - toInternational("628123456789") -> "+62 812-3456-789"
     * - toInternational("+628123456789") -> "+62 812-3456-789"
     *
     * @param string $phone The phone number to format
     * @param bool $withSeparators Whether to include dashes (default: true)
     * @return string The formatted phone number in international format
     * @throws InvalidArgumentException If the phone number is invalid
     */
    public static function toInternational(string $phone, bool $withSeparators = true): string
    {
        $cleaned = self::cleanPhoneNumber($phone);
        $normalized = self::normalizeToE164($cleaned);

        if (! self::isValidLength($normalized)) {
            throw new InvalidArgumentException("Invalid phone number length: {$phone}");
        }

        // Format: +62 8xx-xxxx-xxxx or +62 8xxxxxxxxxx
        if ($withSeparators) {
            $areaCode = substr($normalized, 2, 3); // 8xx
            $firstPart = substr($normalized, 5, 4); // xxxx
            $secondPart = substr($normalized, 9);    // remaining digits

            return '+62 '.$areaCode.'-'.$firstPart.'-'.$secondPart;
        }

        return '+'.$normalized;
    }

    /**
     * Convert to local format (08xx-xxxx-xxxx).
     *
     * Examples:
     * - toLocal("+628123456789") -> "0812-3456-789"
     * - toLocal("628123456789") -> "0812-3456-789"
     * - toLocal("08123456789") -> "0812-3456-789"
     *
     * @param string $phone The phone number to format
     * @param bool $withSeparators Whether to include dashes (default: true)
     * @return string The formatted phone number in local format
     * @throws InvalidArgumentException If the phone number is invalid
     */
    public static function toLocal(string $phone, bool $withSeparators = true): string
    {
        $cleaned = self::cleanPhoneNumber($phone);
        $normalized = self::normalizeToE164($cleaned);

        if (! self::isValidLength($normalized)) {
            throw new InvalidArgumentException("Invalid phone number length: {$phone}");
        }

        // Convert to local format: 628xx... -> 08xx...
        $localNumber = '0'.substr($normalized, 2);

        if ($withSeparators) {
            $areaCode = substr($localNumber, 0, 4); // 08xx
            $firstPart = substr($localNumber, 4, 4); // xxxx
            $secondPart = substr($localNumber, 8);   // remaining digits

            return $areaCode.'-'.$firstPart.'-'.$secondPart;
        }

        return $localNumber;
    }

    /**
     * Convert to E.164 format (628xxxxxxxxxx).
     *
     * Examples:
     * - toE164("08123456789") -> "628123456789"
     * - toE164("+628123456789") -> "628123456789"
     * - toE164("0812-3456-789") -> "628123456789"
     *
     * @param string $phone The phone number to format
     * @return string The formatted phone number in E.164 format (without +)
     * @throws InvalidArgumentException If the phone number is invalid
     */
    public static function toE164(string $phone): string
    {
        $cleaned = self::cleanPhoneNumber($phone);
        $normalized = self::normalizeToE164($cleaned);

        if (! self::isValidLength($normalized)) {
            throw new InvalidArgumentException("Invalid phone number length: {$phone}");
        }

        return $normalized;
    }

    /**
     * Clean phone number by removing non-numeric characters (except +).
     *
     * @param string $phone The phone number to clean
     * @return string The cleaned phone number
     */
    private static function cleanPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, parentheses, and other formatting
        $cleaned = preg_replace('/[^\d+]/', '', trim($phone));

        if ($cleaned === null || $cleaned === '') {
            throw new InvalidArgumentException('Invalid phone number: empty after cleaning');
        }

        return $cleaned;
    }

    /**
     * Normalize phone number to E.164 format (without +).
     *
     * @param string $phone The cleaned phone number
     * @return string The normalized phone number (628xxxxxxxxxx)
     */
    private static function normalizeToE164(string $phone): string
    {
        // Remove leading + if present
        $phone = ltrim($phone, '+');

        // Convert local format to international
        if (str_starts_with($phone, '0')) {
            // 08xx... -> 628xx...
            return '62'.substr($phone, 1);
        }

        // Already in E.164 format (or international without +)
        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        // Assume it needs 62 prefix
        return '62'.$phone;
    }

    /**
     * Check if the phone number has valid length.
     *
     * Indonesian mobile numbers: 62 + 8xx (3) + 7-10 digits = 12-15 total
     *
     * @param string $normalized The normalized phone number (628xxxxxxxxxx)
     * @return bool True if valid length
     */
    private static function isValidLength(string $normalized): bool
    {
        $length = strlen($normalized);

        // Indonesian mobile numbers typically: 62 8xx xxxx xxx(x)
        // Minimum: 628xxxxxxx (11 digits)
        // Maximum: 628xxxxxxxxxxx (15 digits)
        return $length >= 11 && $length <= 15;
    }
}
