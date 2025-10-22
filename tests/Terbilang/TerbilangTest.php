<?php

declare(strict_types=1);

use Awinarko\IndonesiaUtilities\Terbilang\Terbilang;

describe('Terbilang::convert - Basic numbers', function () {
    it('converts zero', function () {
        expect(Terbilang::convert(0))->toBe('nol');
    });

    it('converts single digits (1-9)', function () {
        expect(Terbilang::convert(1))->toBe('satu');
        expect(Terbilang::convert(2))->toBe('dua');
        expect(Terbilang::convert(3))->toBe('tiga');
        expect(Terbilang::convert(4))->toBe('empat');
        expect(Terbilang::convert(5))->toBe('lima');
        expect(Terbilang::convert(6))->toBe('enam');
        expect(Terbilang::convert(7))->toBe('tujuh');
        expect(Terbilang::convert(8))->toBe('delapan');
        expect(Terbilang::convert(9))->toBe('sembilan');
    });

    it('converts teens (10-19)', function () {
        expect(Terbilang::convert(10))->toBe('sepuluh');
        expect(Terbilang::convert(11))->toBe('sebelas');
        expect(Terbilang::convert(12))->toBe('dua belas');
        expect(Terbilang::convert(13))->toBe('tiga belas');
        expect(Terbilang::convert(14))->toBe('empat belas');
        expect(Terbilang::convert(15))->toBe('lima belas');
        expect(Terbilang::convert(16))->toBe('enam belas');
        expect(Terbilang::convert(17))->toBe('tujuh belas');
        expect(Terbilang::convert(18))->toBe('delapan belas');
        expect(Terbilang::convert(19))->toBe('sembilan belas');
    });

    it('converts tens (20-99)', function () {
        expect(Terbilang::convert(20))->toBe('dua puluh');
        expect(Terbilang::convert(25))->toBe('dua puluh lima');
        expect(Terbilang::convert(30))->toBe('tiga puluh');
        expect(Terbilang::convert(45))->toBe('empat puluh lima');
        expect(Terbilang::convert(50))->toBe('lima puluh');
        expect(Terbilang::convert(67))->toBe('enam puluh tujuh');
        expect(Terbilang::convert(89))->toBe('delapan puluh sembilan');
        expect(Terbilang::convert(99))->toBe('sembilan puluh sembilan');
    });
});

describe('Terbilang::convert - Hundreds', function () {
    it('converts exactly 100 as "seratus" (special case)', function () {
        expect(Terbilang::convert(100))->toBe('seratus');
    });

    it('converts hundreds (200-900)', function () {
        expect(Terbilang::convert(200))->toBe('dua ratus');
        expect(Terbilang::convert(300))->toBe('tiga ratus');
        expect(Terbilang::convert(500))->toBe('lima ratus');
        expect(Terbilang::convert(900))->toBe('sembilan ratus');
    });

    it('converts hundreds with remainder', function () {
        expect(Terbilang::convert(101))->toBe('seratus satu');
        expect(Terbilang::convert(125))->toBe('seratus dua puluh lima');
        expect(Terbilang::convert(250))->toBe('dua ratus lima puluh');
        expect(Terbilang::convert(375))->toBe('tiga ratus tujuh puluh lima');
        expect(Terbilang::convert(999))->toBe('sembilan ratus sembilan puluh sembilan');
    });
});

describe('Terbilang::convert - Thousands', function () {
    it('converts exactly 1000 as "seribu" (special case)', function () {
        expect(Terbilang::convert(1000))->toBe('seribu');
    });

    it('converts thousands (2000-999000)', function () {
        expect(Terbilang::convert(2000))->toBe('dua ribu');
        expect(Terbilang::convert(5000))->toBe('lima ribu');
        expect(Terbilang::convert(10000))->toBe('sepuluh ribu');
        expect(Terbilang::convert(25000))->toBe('dua puluh lima ribu');
        expect(Terbilang::convert(100000))->toBe('seratus ribu');
        expect(Terbilang::convert(500000))->toBe('lima ratus ribu');
    });

    it('converts thousands with remainder', function () {
        expect(Terbilang::convert(1001))->toBe('seribu satu');
        expect(Terbilang::convert(1500))->toBe('seribu lima ratus');
        expect(Terbilang::convert(2500))->toBe('dua ribu lima ratus');
        expect(Terbilang::convert(12345))->toBe('dua belas ribu tiga ratus empat puluh lima');
        expect(Terbilang::convert(999999))->toBe('sembilan ratus sembilan puluh sembilan ribu sembilan ratus sembilan puluh sembilan');
    });
});

describe('Terbilang::convert - Millions', function () {
    it('converts millions', function () {
        expect(Terbilang::convert(1000000))->toBe('satu juta');
        expect(Terbilang::convert(2000000))->toBe('dua juta');
        expect(Terbilang::convert(5000000))->toBe('lima juta');
        expect(Terbilang::convert(100000000))->toBe('seratus juta');
    });

    it('converts millions with remainder', function () {
        expect(Terbilang::convert(1000001))->toBe('satu juta satu');
        expect(Terbilang::convert(1500000))->toBe('satu juta lima ratus ribu');
        expect(Terbilang::convert(1234567))->toBe('satu juta dua ratus tiga puluh empat ribu lima ratus enam puluh tujuh');
        expect(Terbilang::convert(25500000))->toBe('dua puluh lima juta lima ratus ribu');
    });
});

describe('Terbilang::convert - Billions', function () {
    it('converts billions (miliar)', function () {
        expect(Terbilang::convert(1000000000))->toBe('satu miliar');
        expect(Terbilang::convert(2000000000))->toBe('dua miliar');
        expect(Terbilang::convert(5000000000))->toBe('lima miliar');
    });

    it('converts billions with remainder', function () {
        expect(Terbilang::convert(1000000001))->toBe('satu miliar satu');
        expect(Terbilang::convert(1500000000))->toBe('satu miliar lima ratus juta');
        expect(Terbilang::convert(2500500000))->toBe('dua miliar lima ratus juta lima ratus ribu');
    });
});

describe('Terbilang::convert - Trillions', function () {
    it('converts trillions (triliun)', function () {
        expect(Terbilang::convert(1000000000000))->toBe('satu triliun');
        expect(Terbilang::convert(2000000000000))->toBe('dua triliun');
        expect(Terbilang::convert(5000000000000))->toBe('lima triliun');
    });

    it('converts trillions with remainder', function () {
        expect(Terbilang::convert(1000000000001))->toBe('satu triliun satu');
        expect(Terbilang::convert(1500000000000))->toBe('satu triliun lima ratus miliar');
        expect(Terbilang::convert(2123456789012))->toBe('dua triliun seratus dua puluh tiga miliar empat ratus lima puluh enam juta tujuh ratus delapan puluh sembilan ribu dua belas');
    });
});

describe('Terbilang::convert - Decimals', function () {
    it('converts decimal numbers with "koma"', function () {
        expect(Terbilang::convert(1.5))->toBe('satu koma lima puluh');
        expect(Terbilang::convert(10.25))->toBe('sepuluh koma dua puluh lima');
        expect(Terbilang::convert(100.50))->toBe('seratus koma lima puluh');
        expect(Terbilang::convert(1000.99))->toBe('seribu koma sembilan puluh sembilan');
    });

    it('converts decimal numbers with single digit after comma', function () {
        expect(Terbilang::convert(1.1))->toBe('satu koma sepuluh');
        expect(Terbilang::convert(5.05))->toBe('lima koma lima');
        expect(Terbilang::convert(100.01))->toBe('seratus koma satu');
    });

    it('handles rounding for decimals beyond 2 places', function () {
        expect(Terbilang::convert(1.234))->toBe('satu koma dua puluh tiga');
        expect(Terbilang::convert(1.999))->toBe('satu koma seratus');
    });
});

describe('Terbilang::convert - Negative numbers', function () {
    it('converts negative numbers with "minus" prefix', function () {
        expect(Terbilang::convert(-1))->toBe('minus satu');
        expect(Terbilang::convert(-100))->toBe('minus seratus');
        expect(Terbilang::convert(-1000))->toBe('minus seribu');
        expect(Terbilang::convert(-1234567))->toBe('minus satu juta dua ratus tiga puluh empat ribu lima ratus enam puluh tujuh');
    });

    it('converts negative decimal numbers', function () {
        expect(Terbilang::convert(-1.5))->toBe('minus satu koma lima puluh');
        expect(Terbilang::convert(-100.25))->toBe('minus seratus koma dua puluh lima');
    });
});

describe('Terbilang::convertRupiah', function () {
    it('converts amounts with "rupiah" suffix', function () {
        expect(Terbilang::convertRupiah(1000))->toBe('seribu rupiah');
        expect(Terbilang::convertRupiah(1500))->toBe('seribu lima ratus rupiah');
        expect(Terbilang::convertRupiah(1000000))->toBe('satu juta rupiah');
        expect(Terbilang::convertRupiah(2500000))->toBe('dua juta lima ratus ribu rupiah');
    });

    it('converts zero rupiah', function () {
        expect(Terbilang::convertRupiah(0))->toBe('nol rupiah');
    });

    it('converts decimal rupiah amounts', function () {
        expect(Terbilang::convertRupiah(1000.50))->toBe('seribu koma lima puluh rupiah');
        expect(Terbilang::convertRupiah(2500.99))->toBe('dua ribu lima ratus koma sembilan puluh sembilan rupiah');
    });

    it('converts negative rupiah amounts', function () {
        expect(Terbilang::convertRupiah(-1000))->toBe('minus seribu rupiah');
        expect(Terbilang::convertRupiah(-2500000))->toBe('minus dua juta lima ratus ribu rupiah');
    });
});

describe('Terbilang::convert - Complex real-world examples', function () {
    it('converts invoice amounts correctly', function () {
        expect(Terbilang::convert(123456789))->toBe('seratus dua puluh tiga juta empat ratus lima puluh enam ribu tujuh ratus delapan puluh sembilan');
        expect(Terbilang::convert(987654321))->toBe('sembilan ratus delapan puluh tujuh juta enam ratus lima puluh empat ribu tiga ratus dua puluh satu');
    });

    it('converts payment amounts with decimals', function () {
        expect(Terbilang::convert(1234567.89))->toBe('satu juta dua ratus tiga puluh empat ribu lima ratus enam puluh tujuh koma delapan puluh sembilan');
    });
});
