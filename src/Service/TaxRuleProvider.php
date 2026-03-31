<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Exception\InvalidRequestDataException;

final class TaxRuleProvider
{
    /**
     * Заглушка под редис: ключ - префикс страны, значение - regex и ставка налога.
     */
    private const RULES = [
        'DE' => [
            'pattern' => '/^DE\d{9}$/',
            'taxRate' => 19,
        ],
        'IT' => [
            'pattern' => '/^IT\d{11}$/',
            'taxRate' => 22,
        ],
        'GR' => [
            'pattern' => '/^GR\d{9}$/',
            'taxRate' => 24,
        ],
        'FR' => [
            'pattern' => '/^FR[A-Z]{2}\d{9}$/',
            'taxRate' => 20,
        ],
    ];

    public function isValidTaxNumber(string $taxNumber): bool
    {
        $rule = $this->getRuleByTaxNumber($taxNumber);
        if ($rule === null) {
            return false;
        }

        return preg_match($rule['pattern'], $this->normalizeTaxNumber($taxNumber)) === 1;
    }

    public function getTaxRateByTaxNumber(string $taxNumber): int
    {
        $rule = $this->getRuleByTaxNumber($taxNumber);

        if ($rule === null) {
            throw InvalidRequestDataException::because('Unsupported tax number country prefix.');
        }

        return $rule['taxRate'];
    }

    /**
     * @param string $taxNumber
     * @return array{pattern: string, taxRate: int}|null
     */
    private function getRuleByTaxNumber(string $taxNumber): ?array
    {
        $countryCode = $this->extractCountryCode($taxNumber);

        return self::RULES[$countryCode] ?? null;
    }

    private function extractCountryCode(string $taxNumber): string
    {
        return substr($this->normalizeTaxNumber($taxNumber), 0, 2);
    }

    private function normalizeTaxNumber(string $taxNumber): string
    {
        return strtoupper(trim($taxNumber));
    }
}
