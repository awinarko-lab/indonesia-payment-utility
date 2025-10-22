<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\Bank;

/**
 * Indonesian bank codes enum.
 *
 * Contains bank codes for major Indonesian banks, used for interbank transfers,
 * payment routing, and validation.
 */
enum BankCode: string
{
    case BRI = '002';
    case BNI = '009';
    case MANDIRI = '008';
    case BCA = '014';
    case BTN = '200';
    case PERMATA = '013';
    case DANAMON = '011';
    case BII_MAYBANK = '016';
    case PANIN = '019';
    case CIMB_NIAGA = '022';
    case UOB = '023';
    case OCBC_NISP = '028';
    case CITIBANK = '031';
    case STANDARD_CHARTERED = '050';
    case HSBC = '087';
    case MEGA = '426';
    case BUKOPIN = '441';
    case BJB = '110';
    case BPD_BALI = '129';
    case BPD_DIY = '112';
    case BPD_JATENG = '113';
    case BPD_JATIM = '114';
    case BPD_KALBAR = '123';
    case BPD_KALSEL = '122';
    case BPD_KALTIM = '124';
    case BSI = '451';  // Bank Syariah Indonesia
    case MUAMALAT = '147';
    case BTPN = '213';
    case JENIUS = '213';  // Jenius (BTPN digital)
    case SEABANK = '535';
    case JAGO = '503';
    case NEO_COMMERCE = '490';

    /**
     * Get the full name of the bank.
     *
     * @return string The bank name
     */
    public function getName(): string
    {
        return match ($this) {
            self::BRI => 'Bank Rakyat Indonesia',
            self::BNI => 'Bank Negara Indonesia',
            self::MANDIRI => 'Bank Mandiri',
            self::BCA => 'Bank Central Asia',
            self::BTN => 'Bank Tabungan Negara',
            self::PERMATA => 'Bank Permata',
            self::DANAMON => 'Bank Danamon',
            self::BII_MAYBANK => 'Maybank Indonesia',
            self::PANIN => 'Bank Panin',
            self::CIMB_NIAGA => 'Bank CIMB Niaga',
            self::UOB => 'United Overseas Bank',
            self::OCBC_NISP => 'Bank OCBC NISP',
            self::CITIBANK => 'Citibank Indonesia',
            self::STANDARD_CHARTERED => 'Standard Chartered Bank',
            self::HSBC => 'HSBC Indonesia',
            self::MEGA => 'Bank Mega',
            self::BUKOPIN => 'Bank Bukopin',
            self::BJB => 'Bank Jabar Banten',
            self::BPD_BALI => 'Bank Pembangunan Daerah Bali',
            self::BPD_DIY => 'Bank Pembangunan Daerah DIY',
            self::BPD_JATENG => 'Bank Pembangunan Daerah Jawa Tengah',
            self::BPD_JATIM => 'Bank Pembangunan Daerah Jawa Timur',
            self::BPD_KALBAR => 'Bank Pembangunan Daerah Kalimantan Barat',
            self::BPD_KALSEL => 'Bank Pembangunan Daerah Kalimantan Selatan',
            self::BPD_KALTIM => 'Bank Pembangunan Daerah Kalimantan Timur',
            self::BSI => 'Bank Syariah Indonesia',
            self::MUAMALAT => 'Bank Muamalat Indonesia',
            self::BTPN => 'Bank BTPN',
            self::JENIUS => 'Jenius',
            self::SEABANK => 'SeaBank Indonesia',
            self::JAGO => 'Bank Jago',
            self::NEO_COMMERCE => 'Bank Neo Commerce',
        };
    }

    /**
     * Get the SWIFT code of the bank (if available).
     *
     * @return string|null The SWIFT code, or null if not available
     */
    public function getSwiftCode(): ?string
    {
        return match ($this) {
            self::BRI => 'BRINIDJA',
            self::BNI => 'BNINIDJA',
            self::MANDIRI => 'BMRIIDJA',
            self::BCA => 'CENAIDJA',
            self::BTN => 'BTANIDJA',
            self::PERMATA => 'BBBAIDJA',
            self::DANAMON => 'BDINIDJA',
            self::BII_MAYBANK => 'MBBEIDJA',
            self::PANIN => 'PINAIDJA',
            self::CIMB_NIAGA => 'BNIAIDJA',
            self::UOB => 'UOVBIDJA',
            self::OCBC_NISP => 'NISPIDJA',
            self::CITIBANK => 'CITIIDJA',
            self::STANDARD_CHARTERED => 'SCBLIDJA',
            self::HSBC => 'HSBCIDJA',
            self::MEGA => 'MEGAIDJA',
            self::BUKOPIN => 'BBUKIDJA',
            default => null,
        };
    }

    /**
     * Check if the bank is a state-owned bank (BUMN).
     *
     * @return bool True if state-owned
     */
    public function isStateOwned(): bool
    {
        return match ($this) {
            self::BRI, self::BNI, self::MANDIRI, self::BTN => true,
            default => false,
        };
    }

    /**
     * Check if the bank is a private bank.
     *
     * @return bool True if private
     */
    public function isPrivate(): bool
    {
        return ! $this->isStateOwned() && ! $this->isRegionalBank();
    }

    /**
     * Check if the bank is a regional development bank (BPD).
     *
     * @return bool True if regional bank
     */
    public function isRegionalBank(): bool
    {
        return match ($this) {
            self::BJB, self::BPD_BALI, self::BPD_DIY, self::BPD_JATENG,
            self::BPD_JATIM, self::BPD_KALBAR, self::BPD_KALSEL, self::BPD_KALTIM => true,
            default => false,
        };
    }
}
