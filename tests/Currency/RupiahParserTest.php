<?php

declare(strict_types=1);

use Awinarko\IndonesiaUtilities\Currency\RupiahParser;

describe('RupiahParser::parse - Indonesian format', function () {
    it('parses Indonesian format with Rp symbol', function () {
        expect(RupiahParser::parse('Rp 1.000,00'))->toBe(1000.0);
        expect(RupiahParser::parse('Rp 10.000,50'))->toBe(10000.5);
        expect(RupiahParser::parse('Rp 1.234.567,89'))->toBe(1234567.89);
    });

    it('parses Indonesian format without Rp symbol', function () {
        expect(RupiahParser::parse('1.000,00'))->toBe(1000.0);
        expect(RupiahParser::parse('10.000,50'))->toBe(10000.5);
        expect(RupiahParser::parse('1.234.567,89'))->toBe(1234567.89);
    });

    it('parses Indonesian format with only comma', function () {
        expect(RupiahParser::parse('1000,50'))->toBe(1000.5);
        expect(RupiahParser::parse('1234,99'))->toBe(1234.99);
    });

    it('parses Indonesian format with thousands separator only', function () {
        expect(RupiahParser::parse('1.000'))->toBe(1000.0);
        expect(RupiahParser::parse('10.000'))->toBe(10000.0);
        expect(RupiahParser::parse('1.000.000'))->toBe(1000000.0);
    });
});

describe('RupiahParser::parse - International format', function () {
    it('parses international format', function () {
        expect(RupiahParser::parse('1,000.00'))->toBe(1000.0);
        expect(RupiahParser::parse('10,000.50'))->toBe(10000.5);
        expect(RupiahParser::parse('1,234,567.89'))->toBe(1234567.89);
    });

    it('parses international format with decimal only', function () {
        expect(RupiahParser::parse('1000.50'))->toBe(1000.5);
        expect(RupiahParser::parse('1234.99'))->toBe(1234.99);
    });
});

describe('RupiahParser::parse - Plain numbers', function () {
    it('parses plain integers', function () {
        expect(RupiahParser::parse('0'))->toBe(0.0);
        expect(RupiahParser::parse('1000'))->toBe(1000.0);
        expect(RupiahParser::parse('1234567'))->toBe(1234567.0);
    });

    it('parses plain floats without separators', function () {
        expect(RupiahParser::parse('1000'))->toBe(1000.0);
        expect(RupiahParser::parse('12345'))->toBe(12345.0);
    });
});

describe('RupiahParser::parse - Negative values', function () {
    it('parses negative values with parentheses', function () {
        expect(RupiahParser::parse('(Rp 1.000,00)'))->toBe(-1000.0);
        expect(RupiahParser::parse('(1.000,00)'))->toBe(-1000.0);
        expect(RupiahParser::parse('(1,000.00)'))->toBe(-1000.0);
    });

    it('parses negative values with minus sign', function () {
        expect(RupiahParser::parse('-Rp 1.000,00'))->toBe(-1000.0);
        expect(RupiahParser::parse('-1.000,00'))->toBe(-1000.0);
        expect(RupiahParser::parse('-1000'))->toBe(-1000.0);
    });
});

describe('RupiahParser::parse - Edge cases', function () {
    it('parses zero in various formats', function () {
        expect(RupiahParser::parse('0'))->toBe(0.0);
        expect(RupiahParser::parse('Rp 0'))->toBe(0.0);
        expect(RupiahParser::parse('0,00'))->toBe(0.0);
        expect(RupiahParser::parse('0.00'))->toBe(0.0);
    });

    it('handles extra spaces', function () {
        expect(RupiahParser::parse('  Rp 1.000,00  '))->toBe(1000.0);
        expect(RupiahParser::parse('Rp  1.000,00'))->toBe(1000.0);
    });

    it('parses large numbers correctly', function () {
        expect(RupiahParser::parse('Rp 999.999.999,99'))->toBe(999999999.99);
        expect(RupiahParser::parse('1,000,000,000.00'))->toBe(1000000000.0);
    });

    it('parses very small decimal values', function () {
        expect(RupiahParser::parse('0,01'))->toBe(0.01);
        expect(RupiahParser::parse('0.01'))->toBe(0.01);
        expect(RupiahParser::parse('Rp 0,99'))->toBe(0.99);
    });
});

describe('RupiahParser::parse - Error handling', function () {
    it('throws exception for empty string', function () {
        expect(fn () => RupiahParser::parse(''))->toThrow(InvalidArgumentException::class);
    });

    it('throws exception for whitespace only', function () {
        expect(fn () => RupiahParser::parse('   '))->toThrow(InvalidArgumentException::class);
    });

    it('throws exception for symbol only', function () {
        expect(fn () => RupiahParser::parse('Rp'))->toThrow(InvalidArgumentException::class);
    });

    it('throws exception for invalid characters', function () {
        expect(fn () => RupiahParser::parse('Rp abc'))->toThrow(InvalidArgumentException::class);
        expect(fn () => RupiahParser::parse('1.000.abc'))->toThrow(InvalidArgumentException::class);
    });

    it('throws exception for multiple decimal separators', function () {
        expect(fn () => RupiahParser::parse('1,00,00'))->toThrow(InvalidArgumentException::class);
    });
});

describe('RupiahParser::parseToInt', function () {
    it('parses to integer', function () {
        expect(RupiahParser::parseToInt('Rp 1.000,00'))->toBe(1000);
        expect(RupiahParser::parseToInt('1.234,00'))->toBe(1234);
        expect(RupiahParser::parseToInt('1,234.00'))->toBe(1234);
    });

    it('rounds to nearest integer', function () {
        expect(RupiahParser::parseToInt('Rp 1.000,50'))->toBe(1001);
        expect(RupiahParser::parseToInt('Rp 1.000,49'))->toBe(1000);
        expect(RupiahParser::parseToInt('1.234,99'))->toBe(1235);
        expect(RupiahParser::parseToInt('1.234,01'))->toBe(1234);
    });

    it('handles negative values', function () {
        expect(RupiahParser::parseToInt('(Rp 1.000,50)'))->toBe(-1001);
        expect(RupiahParser::parseToInt('-1.234,99'))->toBe(-1235);
    });

    it('handles zero', function () {
        expect(RupiahParser::parseToInt('0'))->toBe(0);
        expect(RupiahParser::parseToInt('Rp 0,00'))->toBe(0);
    });
});

describe('RupiahParser::parse - Round-trip with formatter', function () {
    it('successfully round-trips with RupiahFormatter', function () {
        $amounts = [0, 1000, 1234.56, 999999.99, -1000, -1234.56];

        foreach ($amounts as $amount) {
            $formatted = \Awinarko\IndonesiaUtilities\Currency\RupiahFormatter::format($amount);
            $parsed = RupiahParser::parse($formatted);

            expect($parsed)->toBe((float) $amount);
        }
    });
});

describe('RupiahParser::parse - Real-world examples', function () {
    it('parses common payment amounts', function () {
        expect(RupiahParser::parse('Rp 50.000'))->toBe(50000.0);
        expect(RupiahParser::parse('Rp 100.000'))->toBe(100000.0);
        expect(RupiahParser::parse('Rp 250.000'))->toBe(250000.0);
        expect(RupiahParser::parse('Rp 1.000.000'))->toBe(1000000.0);
    });

    it('parses invoice amounts with decimals', function () {
        expect(RupiahParser::parse('Rp 1.234.567,89'))->toBe(1234567.89);
        expect(RupiahParser::parse('Rp 987.654,32'))->toBe(987654.32);
    });

    it('parses refund amounts (negative)', function () {
        expect(RupiahParser::parse('(Rp 50.000,00)'))->toBe(-50000.0);
        expect(RupiahParser::parse('(Rp 100.000,00)'))->toBe(-100000.0);
    });
});
