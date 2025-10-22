<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\Nik;

use DateTime;

/**
 * Validates Indonesian National Identity Number (NIK).
 *
 * NIK format: PPKKSSDDMMYYKKKK (16 digits)
 * - PP: Province code (2 digits)
 * - KK: Regency/city code (2 digits)
 * - SS: District code (2 digits)
 * - DD: Birth day (2 digits, +40 for females)
 * - MM: Birth month (2 digits)
 * - YY: Birth year (2 digits)
 * - KKKK: Sequential number (4 digits)
 */
final class NikValidator
{
    /**
     * Valid province codes (11-94).
     *
     * @var array<int>
     */
    private const VALID_PROVINCES = [
        11, 12, 13, 14, 15, 16, 17, 18, 19, 21,
        31, 32, 33, 34, 35, 36, 51, 52, 53, 61,
        62, 63, 64, 65, 71, 72, 73, 74, 75, 76,
        81, 82, 91, 92, 93, 94,
    ];

    /**
     * Validate a NIK number.
     *
     * @param string $nik The NIK to validate
     * @return bool True if valid
     */
    public static function validate(string $nik): bool
    {
        $errors = self::validateWithErrors($nik);

        return count($errors) === 0;
    }

    /**
     * Validate a NIK and return detailed error messages.
     *
     * @param string $nik The NIK to validate
     * @return array<string> Array of error messages (empty if valid)
     */
    public static function validateWithErrors(string $nik): array
    {
        $errors = [];

        // Remove any non-digit characters
        $cleaned = preg_replace('/\D/', '', $nik);

        // Check length
        if (strlen($cleaned) !== 16) {
            $errors[] = 'NIK must be exactly 16 digits';

            return $errors;
        }

        // Extract components
        $provinceCode = (int) substr($cleaned, 0, 2);
        $regencyCode = (int) substr($cleaned, 2, 2);
        $districtCode = (int) substr($cleaned, 4, 2);
        $day = (int) substr($cleaned, 6, 2);
        $month = (int) substr($cleaned, 8, 2);
        $year = (int) substr($cleaned, 10, 2);

        // Validate province code
        if (! in_array($provinceCode, self::VALID_PROVINCES, true)) {
            $errors[] = "Invalid province code: {$provinceCode}";
        }

        // Validate regency code (01-99)
        if ($regencyCode < 1 || $regencyCode > 99) {
            $errors[] = "Invalid regency code: {$regencyCode}";
        }

        // Validate district code (01-99)
        if ($districtCode < 1 || $districtCode > 99) {
            $errors[] = "Invalid district code: {$districtCode}";
        }

        // Validate month (01-12)
        if ($month < 1 || $month > 12) {
            $errors[] = "Invalid month: {$month}";

            return $errors; // Can't validate day without valid month
        }

        // Validate day (1-31 for males, 41-71 for females)
        $actualDay = $day > 40 ? $day - 40 : $day;
        if ($actualDay < 1 || $actualDay > 31) {
            $errors[] = "Invalid day: {$day}";

            return $errors;
        }

        // Validate date
        $currentYear = (int) date('y');
        $fullYear = $year <= $currentYear ? 2000 + $year : 1900 + $year;

        if (! checkdate($month, $actualDay, $fullYear)) {
            $errors[] = "Invalid date: {$actualDay}/{$month}/{$fullYear}";
        }

        // Check if birth date is not in the future
        $birthDate = DateTime::createFromFormat('Y-m-d', "{$fullYear}-{$month}-{$actualDay}");
        $today = new DateTime;

        if ($birthDate > $today) {
            $errors[] = 'Birth date cannot be in the future';
        }

        return $errors;
    }
}
