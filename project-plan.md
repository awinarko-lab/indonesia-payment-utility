# Indonesia Utilities Package - Project Plan

## Overview

A comprehensive PHP package providing utilities for working with Indonesian-specific data formats, particularly useful for payment gateway and financial applications.

## Package Name Suggestion

`indonesia-utilities` or `id-helpers`

## Module Implementation Plan

### 1. Currency Module (Priority: HIGH)

**Location**: `src/Currency/`

#### RupiahFormatter

**Purpose**: Format numeric values as Indonesian Rupiah currency strings

**Public API**:
```php
RupiahFormatter::format(int|float $amount, bool $withSymbol = true): string
RupiahFormatter::formatCompact(int|float $amount): string // 1.5M, 2.3K, etc.
```

**Features**:
- Format: `Rp 1.000.000,00` (with proper thousand separator and decimal)
- Options: with/without symbol, with/without decimals
- Compact notation for large numbers (1M, 1B, 1T)
- Handle negative values: `(Rp 1.000.000,00)` or `Rp -1.000.000,00`

**Test Cases**:
- Zero, positive, negative values
- Decimal handling
- Large numbers (billions, trillions)
- Compact format edge cases

#### RupiahParser

**Purpose**: Parse Rupiah strings back to numeric values

**Public API**:
```php
RupiahParser::parse(string $rupiah): float
RupiahParser::parseToInt(string $rupiah): int
```

**Features**:
- Parse various formats: `Rp 1.000,00`, `1.000`, `1000`, `1,000.00`
- Handle with/without Rp symbol
- Handle negative values
- Throw exception on invalid format

**Test Cases**:
- Valid formats with different separators
- Invalid formats should throw exceptions
- Round-trip testing (format → parse → format)

---

### 2. Terbilang Module (Priority: HIGH)

**Location**: `src/Terbilang/`

#### Terbilang

**Purpose**: Convert numbers to Indonesian words (legally required for invoices/receipts)

**Public API**:
```php
Terbilang::convert(int|float $number): string
Terbilang::convertRupiah(int|float $amount): string // adds "rupiah"
```

**Features**:
- Convert 0-999,999,999,999,999 (up to triliun)
- Proper Indonesian grammar: "seribu" not "satu ribu"
- Handle decimals: "seribu lima ratus koma dua puluh lima"
- Currency mode: "satu juta rupiah"

**Examples**:
- `1000` → `"seribu"`
- `1500` → `"seribu lima ratus"`
- `1500.25` → `"seribu lima ratus koma dua puluh lima"`
- `1000000` → `"satu juta rupiah"` (with convertRupiah)

**Implementation Notes**:
- Units: satuan (1-9), puluhan (10-90), ratusan (100-900)
- Groups: ribu, juta, miliar, triliun
- Special case: 1000 = "seribu" not "satu ribu"
- Special case: 11-19 handling (sebelas, dua belas, etc.)

**Test Cases**:
- Single digits (0-9)
- Teens (10-19)
- Tens (20, 30, ... 90)
- Hundreds (100, 200, ... 900)
- Thousands with special "seribu" case
- Millions, billions, trillions
- Complex numbers: 123,456,789
- Decimals
- Edge cases: 0, negative numbers

---

### 3. NIK (National ID) Module (Priority: MEDIUM)

**Location**: `src/Nik/`

#### NIK Format Specification

16 digits: `PPKKSSDDMMYYKKKK`
- `PP` (2 digits): Province code (11-94)
- `KK` (2 digits): Regency/city code
- `SS` (2 digits): District code
- `DD` (2 digits): Birth day (female +40)
- `MM` (2 digits): Birth month
- `YY` (2 digits): Birth year
- `KKKK` (4 digits): Sequential number

#### NikValidator

**Purpose**: Validate NIK format and checksum

**Public API**:
```php
NikValidator::validate(string $nik): bool
NikValidator::validateWithErrors(string $nik): array // returns error details
```

**Validation Rules**:
- Exactly 16 digits
- Province code exists (11-94, validate against known codes)
- Birth date is valid (day 1-31 or 41-71 for females)
- Month is valid (01-12)
- Year is plausible (not future)

**Test Cases**:
- Valid NIK (male and female)
- Invalid length
- Invalid province code
- Invalid birth date
- Invalid month
- Future dates

#### NikParser

**Purpose**: Extract information from valid NIK

**Public API**:
```php
NikParser::parse(string $nik): NikData
// NikData contains: province, regency, district, birthDate, gender, sequenceNumber
NikParser::getProvince(string $nik): string
NikParser::getBirthDate(string $nik): DateTimeImmutable
NikParser::getGender(string $nik): Gender // enum: Male/Female
```

**Features**:
- Extract province code
- Parse birth date (handle +40 for females)
- Determine gender
- Return structured data object

**Test Cases**:
- Male NIK parsing
- Female NIK parsing (day +40)
- Different provinces
- Different years (1900s, 2000s)

---

### 4. Phone Number Module (Priority: MEDIUM)

**Location**: `src/PhoneNumber/`

#### Indonesian Phone Format Specifications

Mobile formats:
- `08xx-xxxx-xxxx` (local format)
- `+62 8xx-xxxx-xxxx` (international)
- `628xxxxxxxxxx` (without +)

Landline formats:
- `021-xxxx-xxxx` (Jakarta)
- `031-xxxx-xxxx` (Surabaya)

Major operators:
- Telkomsel: 0811, 0812, 0813, 0821, 0822, 0823, 0852, 0853
- Indosat: 0814, 0815, 0816, 0855, 0856, 0857, 0858
- XL: 0817, 0818, 0819, 0859, 0877, 0878
- Tri: 0895, 0896, 0897, 0898, 0899
- Smartfren: 0881, 0882, 0883, 0884, 0885, 0886, 0887, 0888, 0889

#### PhoneNumberValidator

**Purpose**: Validate Indonesian phone numbers

**Public API**:
```php
PhoneNumberValidator::validate(string $phone): bool
PhoneNumberValidator::isMobile(string $phone): bool
PhoneNumberValidator::isLandline(string $phone): bool
PhoneNumberValidator::getOperator(string $phone): ?string
```

**Features**:
- Validate format
- Detect mobile vs landline
- Identify operator from prefix
- Support multiple input formats

**Test Cases**:
- Valid mobile numbers (all operators)
- Valid landline numbers
- Invalid formats
- Edge cases (too short, too long)

#### PhoneNumberFormatter

**Purpose**: Normalize and format phone numbers

**Public API**:
```php
PhoneNumberFormatter::toInternational(string $phone): string // +62xxx
PhoneNumberFormatter::toLocal(string $phone): string // 08xxx
PhoneNumberFormatter::toE164(string $phone): string // 62xxx (no +)
PhoneNumberFormatter::format(string $phone, Format $format): string
```

**Features**:
- Convert between formats: 08xx ↔ +62 8xx ↔ 62 8xx
- Add formatting separators: +62 812-3456-7890
- Strip non-numeric characters
- Normalize input

**Test Cases**:
- Format conversions
- Various input formats with spaces/dashes
- Round-trip conversions

---

### 5. Bank Module (Priority: MEDIUM)

**Location**: `src/Bank/`

#### BankCode

**Purpose**: Enum of Indonesian bank codes (used for transfers, validation)

**Implementation**: PHP 8.1+ Enum

**Major Banks to Include**:
```php
enum BankCode: string
{
    case BCA = '014';
    case MANDIRI = '008';
    case BRI = '002';
    case BNI = '009';
    case CIMB_NIAGA = '022';
    case PERMATA = '013';
    case DANAMON = '011';
    case BTN = '200';
    case BII_MAYBANK = '016';
    case PANIN = '019';
    case OCBC_NISP = '028';
    case MEGA = '426';
    case CITIBANK = '031';
    case UOB = '023';
    case HSBC = '087';
    case STANDARD_CHARTERED = '050';
    case BUKOPIN = '441';
    case BJB = '110';
    case BPD_BALI = '129';
    // ... add more

    public function getName(): string;
    public function getSwiftCode(): ?string;
}
```

**Features**:
- Bank code to name mapping
- SWIFT codes (for international transfers)
- Helper methods on enum

**Test Cases**:
- All bank codes are valid
- getName() returns correct names
- SWIFT codes where applicable

#### BankCodeResolver

**Purpose**: Resolve bank information from codes or partial matches

**Public API**:
```php
BankCodeResolver::fromCode(string $code): ?BankCode
BankCodeResolver::fromName(string $name): ?BankCode // fuzzy match
BankCodeResolver::all(): array
BankCodeResolver::search(string $query): array
```

**Features**:
- Get bank from code
- Search by name (fuzzy matching)
- List all banks
- Validate account number patterns (optional, advanced)

**Test Cases**:
- Exact code matching
- Name search (case insensitive)
- Partial name matching
- Non-existent codes

---

## Implementation Order

### Phase 1: Core Utilities (Week 1)
1. **Rupiah Formatter** - Most immediately useful for payment gateway
2. **Terbilang** - Required for invoices/receipts (legal requirement)
3. **Rupiah Parser** - Complete the currency module

### Phase 2: Validation Utilities (Week 2)
4. **Phone Number Formatter** - Used in customer data
5. **Phone Number Validator** - Validation for customer registration
6. **Bank Code** - Essential for payment routing

### Phase 3: Advanced Features (Week 3)
7. **NIK Validator** - KYC compliance
8. **NIK Parser** - Customer data extraction
9. **Bank Code Resolver** - Enhanced bank utilities

---

## Testing Strategy

### Coverage Requirements
- 100% code coverage (enforced by composer test:unit)
- All public methods must have tests
- Edge cases and error conditions

### Test Organization
- One test file per class
- Use Pest's descriptive syntax
- Group related tests with `describe()`
- Use data providers for multiple test cases

### Example Test Structure
```php
describe('RupiahFormatter::format', function () {
    it('formats zero correctly', function () {
        expect(RupiahFormatter::format(0))->toBe('Rp 0,00');
    });

    it('formats thousands with separator', function () {
        expect(RupiahFormatter::format(1000))->toBe('Rp 1.000,00');
    });

    it('handles negative values', function () {
        expect(RupiahFormatter::format(-1000))->toBe('(Rp 1.000,00)');
    });
});
```

---

## Quality Checks

Before each module is complete:
1. ✅ `composer test:types` - PHPStan passes
2. ✅ `composer test:lint` - Code style passes
3. ✅ `composer test:unit` - 100% coverage
4. ✅ `composer test:refacto` - No refactoring suggestions
5. ✅ `composer test` - Full suite passes

---

## Documentation

For each public class/method, include:
- PHPDoc with description
- `@param` and `@return` types
- `@throws` for exceptions
- Usage examples in docblocks

---

## Next Steps

1. Update `composer.json` with new package name and description
2. Remove `src/Example.php` and `tests/Feature.php` (skeleton examples)
3. Start with Phase 1: Implement RupiahFormatter first
4. Ensure each implementation passes all quality checks before moving to next
