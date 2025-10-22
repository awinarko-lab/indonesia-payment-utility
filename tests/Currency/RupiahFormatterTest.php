<?php

declare(strict_types=1);

use Awinarko\IndonesiaUtilities\Currency\RupiahFormatter;

describe('RupiahFormatter::format', function () {
    it('formats zero correctly', function () {
        expect(RupiahFormatter::format(0))->toBe('Rp 0,00');
    });

    it('formats positive integers with thousands separator', function () {
        expect(RupiahFormatter::format(1000))->toBe('Rp 1.000,00');
        expect(RupiahFormatter::format(10000))->toBe('Rp 10.000,00');
        expect(RupiahFormatter::format(100000))->toBe('Rp 100.000,00');
        expect(RupiahFormatter::format(1000000))->toBe('Rp 1.000.000,00');
    });

    it('formats decimal values correctly', function () {
        expect(RupiahFormatter::format(1000.50))->toBe('Rp 1.000,50');
        expect(RupiahFormatter::format(1234.99))->toBe('Rp 1.234,99');
        expect(RupiahFormatter::format(999.01))->toBe('Rp 999,01');
    });

    it('formats negative values with parentheses', function () {
        expect(RupiahFormatter::format(-1000))->toBe('(Rp 1.000,00)');
        expect(RupiahFormatter::format(-1234.56))->toBe('(Rp 1.234,56)');
        expect(RupiahFormatter::format(-999999.99))->toBe('(Rp 999.999,99)');
    });

    it('formats without symbol when requested', function () {
        expect(RupiahFormatter::format(1000, false))->toBe('1.000,00');
        expect(RupiahFormatter::format(1234.56, false))->toBe('1.234,56');
    });

    it('formats without decimals when requested', function () {
        expect(RupiahFormatter::format(1000, true, false))->toBe('Rp 1.000');
        expect(RupiahFormatter::format(1234.99, true, false))->toBe('Rp 1.235');
        expect(RupiahFormatter::format(1234.49, true, false))->toBe('Rp 1.234');
    });

    it('formats without symbol and without decimals', function () {
        expect(RupiahFormatter::format(1000, false, false))->toBe('1.000');
        expect(RupiahFormatter::format(-1000, false, false))->toBe('(1.000)');
    });

    it('formats large numbers correctly', function () {
        expect(RupiahFormatter::format(1000000000))->toBe('Rp 1.000.000.000,00');
        expect(RupiahFormatter::format(1234567890.12))->toBe('Rp 1.234.567.890,12');
        expect(RupiahFormatter::format(999999999999.99))->toBe('Rp 999.999.999.999,99');
    });

    it('handles very small positive values', function () {
        expect(RupiahFormatter::format(1))->toBe('Rp 1,00');
        expect(RupiahFormatter::format(0.01))->toBe('Rp 0,01');
        expect(RupiahFormatter::format(0.99))->toBe('Rp 0,99');
    });
});

describe('RupiahFormatter::formatCompact', function () {
    it('formats thousands with K suffix', function () {
        expect(RupiahFormatter::formatCompact(1000))->toBe('Rp 1K');
        expect(RupiahFormatter::formatCompact(1500))->toBe('Rp 1,5K');
        expect(RupiahFormatter::formatCompact(10000))->toBe('Rp 10K');
        expect(RupiahFormatter::formatCompact(999000))->toBe('Rp 999K');
    });

    it('formats millions with M suffix', function () {
        expect(RupiahFormatter::formatCompact(1000000))->toBe('Rp 1M');
        expect(RupiahFormatter::formatCompact(1500000))->toBe('Rp 1,5M');
        expect(RupiahFormatter::formatCompact(25000000))->toBe('Rp 25M');
        expect(RupiahFormatter::formatCompact(999999999))->toBe('Rp 1000M');
    });

    it('formats billions with B suffix', function () {
        expect(RupiahFormatter::formatCompact(1000000000))->toBe('Rp 1B');
        expect(RupiahFormatter::formatCompact(1500000000))->toBe('Rp 1,5B');
        expect(RupiahFormatter::formatCompact(25000000000))->toBe('Rp 25B');
        expect(RupiahFormatter::formatCompact(999999999999))->toBe('Rp 1000B');
    });

    it('formats trillions with T suffix', function () {
        expect(RupiahFormatter::formatCompact(1000000000000))->toBe('Rp 1T');
        expect(RupiahFormatter::formatCompact(1500000000000))->toBe('Rp 1,5T');
        expect(RupiahFormatter::formatCompact(25000000000000))->toBe('Rp 25T');
        expect(RupiahFormatter::formatCompact(999999999999999))->toBe('Rp 1000T');
    });

    it('formats values less than 1000 using standard format', function () {
        expect(RupiahFormatter::formatCompact(0))->toBe('Rp 0');
        expect(RupiahFormatter::formatCompact(1))->toBe('Rp 1');
        expect(RupiahFormatter::formatCompact(999))->toBe('Rp 999');
    });

    it('removes trailing zeros from decimals', function () {
        expect(RupiahFormatter::formatCompact(1000000))->toBe('Rp 1M');
        expect(RupiahFormatter::formatCompact(1100000))->toBe('Rp 1,1M');
        expect(RupiahFormatter::formatCompact(2000000))->toBe('Rp 2M');
    });

    it('formats negative values with parentheses', function () {
        expect(RupiahFormatter::formatCompact(-1000))->toBe('(Rp 1K)');
        expect(RupiahFormatter::formatCompact(-1500000))->toBe('(Rp 1,5M)');
        expect(RupiahFormatter::formatCompact(-2000000000))->toBe('(Rp 2B)');
        expect(RupiahFormatter::formatCompact(-3500000000000))->toBe('(Rp 3,5T)');
    });

    it('formats without symbol when requested', function () {
        expect(RupiahFormatter::formatCompact(1000, false))->toBe('1K');
        expect(RupiahFormatter::formatCompact(1500000, false))->toBe('1,5M');
        expect(RupiahFormatter::formatCompact(2000000000, false))->toBe('2B');
    });

    it('handles edge cases for scale boundaries', function () {
        expect(RupiahFormatter::formatCompact(999))->toBe('Rp 999');
        expect(RupiahFormatter::formatCompact(1000))->toBe('Rp 1K');
        expect(RupiahFormatter::formatCompact(999999))->toBe('Rp 1000K');
        expect(RupiahFormatter::formatCompact(1000000))->toBe('Rp 1M');
    });

    it('formats decimal compact values correctly', function () {
        expect(RupiahFormatter::formatCompact(1234567))->toBe('Rp 1,2M');
        expect(RupiahFormatter::formatCompact(9876543))->toBe('Rp 9,9M');
        expect(RupiahFormatter::formatCompact(1234567890))->toBe('Rp 1,2B');
    });
});
