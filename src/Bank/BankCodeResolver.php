<?php

declare(strict_types=1);

namespace Awinarko\IndonesiaUtilities\Bank;

/**
 * Resolves bank information from bank codes.
 *
 * Provides methods to search for banks by code or name, with fuzzy matching support.
 */
final class BankCodeResolver
{
    /**
     * Get a bank by its code.
     *
     * @param string $code The bank code
     * @return BankCode|null The bank enum, or null if not found
     */
    public static function fromCode(string $code): ?BankCode
    {
        return BankCode::tryFrom($code);
    }

    /**
     * Get a bank by its name (fuzzy matching).
     *
     * @param string $name The bank name (case-insensitive, partial match allowed)
     * @return BankCode|null The bank enum, or null if not found
     */
    public static function fromName(string $name): ?BankCode
    {
        $normalized = strtolower(trim($name));

        foreach (BankCode::cases() as $bank) {
            $bankName = strtolower($bank->getName());

            // Exact match
            if ($bankName === $normalized) {
                return $bank;
            }

            // Partial match (contains)
            if (str_contains($bankName, $normalized)) {
                return $bank;
            }

            // Check if query contains bank name
            if (str_contains($normalized, $bankName)) {
                return $bank;
            }
        }

        return null;
    }

    /**
     * Get all available banks.
     *
     * @return array<BankCode> Array of all bank codes
     */
    public static function all(): array
    {
        return BankCode::cases();
    }

    /**
     * Search for banks by query (code or name).
     *
     * @param string $query The search query
     * @return array<BankCode> Array of matching banks
     */
    public static function search(string $query): array
    {
        $normalized = strtolower(trim($query));
        $results = [];

        foreach (BankCode::cases() as $bank) {
            // Match by code
            if (str_contains(strtolower($bank->value), $normalized)) {
                $results[] = $bank;

                continue;
            }

            // Match by name
            $bankName = strtolower($bank->getName());
            if (str_contains($bankName, $normalized) || str_contains($normalized, $bankName)) {
                $results[] = $bank;
            }
        }

        return $results;
    }

    /**
     * Get all state-owned banks (BUMN).
     *
     * @return array<BankCode> Array of state-owned banks
     */
    public static function getStateOwnedBanks(): array
    {
        return array_filter(BankCode::cases(), fn (BankCode $bank) => $bank->isStateOwned());
    }

    /**
     * Get all private banks.
     *
     * @return array<BankCode> Array of private banks
     */
    public static function getPrivateBanks(): array
    {
        return array_filter(BankCode::cases(), fn (BankCode $bank) => $bank->isPrivate());
    }

    /**
     * Get all regional development banks (BPD).
     *
     * @return array<BankCode> Array of regional banks
     */
    public static function getRegionalBanks(): array
    {
        return array_filter(BankCode::cases(), fn (BankCode $bank) => $bank->isRegionalBank());
    }
}
