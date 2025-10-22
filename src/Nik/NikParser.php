<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\Nik;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Parses Indonesian National Identity Number (NIK) to extract information.
 *
 * Extracts province, regency, district, birth date, gender, and sequence number
 * from a valid NIK.
 */
final class NikParser
{
    /**
     * Province names mapped to codes.
     *
     * @var array<int, string>
     */
    private const PROVINCE_NAMES = [
        11 => 'Aceh',
        12 => 'Sumatera Utara',
        13 => 'Sumatera Barat',
        14 => 'Riau',
        15 => 'Jambi',
        16 => 'Sumatera Selatan',
        17 => 'Bengkulu',
        18 => 'Lampung',
        19 => 'Kepulauan Bangka Belitung',
        21 => 'Kepulauan Riau',
        31 => 'DKI Jakarta',
        32 => 'Jawa Barat',
        33 => 'Jawa Tengah',
        34 => 'DI Yogyakarta',
        35 => 'Jawa Timur',
        36 => 'Banten',
        51 => 'Bali',
        52 => 'Nusa Tenggara Barat',
        53 => 'Nusa Tenggara Timur',
        61 => 'Kalimantan Barat',
        62 => 'Kalimantan Tengah',
        63 => 'Kalimantan Selatan',
        64 => 'Kalimantan Timur',
        65 => 'Kalimantan Utara',
        71 => 'Sulawesi Utara',
        72 => 'Sulawesi Tengah',
        73 => 'Sulawesi Selatan',
        74 => 'Sulawesi Tenggara',
        75 => 'Gorontalo',
        76 => 'Sulawesi Barat',
        81 => 'Maluku',
        82 => 'Maluku Utara',
        91 => 'Papua Barat',
        92 => 'Papua',
        93 => 'Papua Tengah',
        94 => 'Papua Pegunungan',
    ];

    /**
     * Get the province code from NIK.
     *
     * @param string $nik The NIK
     * @return string The province code (2 digits)
     * @throws InvalidArgumentException If NIK is invalid
     */
    public static function getProvinceCode(string $nik): string
    {
        self::validateNik($nik);

        return substr(preg_replace('/\D/', '', $nik), 0, 2);
    }

    /**
     * Get the province name from NIK.
     *
     * @param string $nik The NIK
     * @return string|null The province name, or null if not found
     * @throws InvalidArgumentException If NIK is invalid
     */
    public static function getProvinceName(string $nik): ?string
    {
        $code = (int) self::getProvinceCode($nik);

        return self::PROVINCE_NAMES[$code] ?? null;
    }

    /**
     * Get the birth date from NIK.
     *
     * @param string $nik The NIK
     * @return DateTimeImmutable The birth date
     * @throws InvalidArgumentException If NIK is invalid
     */
    public static function getBirthDate(string $nik): DateTimeImmutable
    {
        self::validateNik($nik);

        $cleaned = preg_replace('/\D/', '', $nik);
        $day = (int) substr($cleaned, 6, 2);
        $month = (int) substr($cleaned, 8, 2);
        $year = (int) substr($cleaned, 10, 2);

        // Remove +40 for females
        $actualDay = $day > 40 ? $day - 40 : $day;

        // Determine full year (assume 1900s if > current year, otherwise 2000s)
        $currentYear = (int) date('y');
        $fullYear = $year <= $currentYear ? 2000 + $year : 1900 + $year;

        $birthDate = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            sprintf('%04d-%02d-%02d', $fullYear, $month, $actualDay)
        );

        if ($birthDate === false) {
            throw new InvalidArgumentException('Invalid birth date in NIK');
        }

        return $birthDate;
    }

    /**
     * Get the gender from NIK.
     *
     * @param string $nik The NIK
     * @return string 'male' or 'female'
     * @throws InvalidArgumentException If NIK is invalid
     */
    public static function getGender(string $nik): string
    {
        self::validateNik($nik);

        $cleaned = preg_replace('/\D/', '', $nik);
        $day = (int) substr($cleaned, 6, 2);

        // Day > 40 indicates female
        return $day > 40 ? 'female' : 'male';
    }

    /**
     * Get the sequence number from NIK.
     *
     * @param string $nik The NIK
     * @return string The 4-digit sequence number
     * @throws InvalidArgumentException If NIK is invalid
     */
    public static function getSequenceNumber(string $nik): string
    {
        self::validateNik($nik);

        return substr(preg_replace('/\D/', '', $nik), 12, 4);
    }

    /**
     * Parse NIK and return all extracted information as an array.
     *
     * @param string $nik The NIK
     * @return array{provinceCode: string, provinceName: string|null, regencyCode: string, districtCode: string, birthDate: DateTimeImmutable, gender: string, sequenceNumber: string}
     * @throws InvalidArgumentException If NIK is invalid
     */
    public static function parse(string $nik): array
    {
        self::validateNik($nik);

        $cleaned = preg_replace('/\D/', '', $nik);

        return [
            'provinceCode' => substr($cleaned, 0, 2),
            'provinceName' => self::getProvinceName($nik),
            'regencyCode' => substr($cleaned, 2, 2),
            'districtCode' => substr($cleaned, 4, 2),
            'birthDate' => self::getBirthDate($nik),
            'gender' => self::getGender($nik),
            'sequenceNumber' => substr($cleaned, 12, 4),
        ];
    }

    /**
     * Validate NIK before parsing.
     *
     * @param string $nik The NIK
     * @throws InvalidArgumentException If NIK is invalid
     */
    private static function validateNik(string $nik): void
    {
        if (! NikValidator::validate($nik)) {
            $errors = NikValidator::validateWithErrors($nik);
            throw new InvalidArgumentException('Invalid NIK: '.implode(', ', $errors));
        }
    }
}
