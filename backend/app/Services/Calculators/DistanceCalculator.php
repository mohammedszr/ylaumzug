<?php

namespace App\Services\Calculators;

class DistanceCalculator
{
    /**
     * Calculate distance between German postal codes
     * 
     * This is a simplified estimation based on postal code patterns.
     * In production, you would use Google Maps Distance Matrix API.
     */
    public function calculateDistance(string $fromPostalCode, string $toPostalCode): int
    {
        // Same postal code = no distance
        if ($fromPostalCode === $toPostalCode) {
            return 0;
        }

        // Clean postal codes (remove spaces, ensure 5 digits)
        $fromCode = $this->cleanPostalCode($fromPostalCode);
        $toCode = $this->cleanPostalCode($toPostalCode);

        if (!$fromCode || !$toCode) {
            return 50; // Default distance for invalid codes
        }

        $fromInt = (int) $fromCode;
        $toInt = (int) $toCode;
        $difference = abs($fromInt - $toInt);

        // Get regions (first digit of postal code)
        $fromRegion = (int) substr($fromCode, 0, 1);
        $toRegion = (int) substr($toCode, 0, 1);

        // Same region (first digit)
        if ($fromRegion === $toRegion) {
            return $this->calculateSameRegionDistance($difference);
        }

        // Different regions
        return $this->calculateCrossRegionDistance($fromRegion, $toRegion);
    }

    /**
     * Calculate distance within the same region
     */
    private function calculateSameRegionDistance(int $difference): int
    {
        if ($difference < 50) return 10;
        if ($difference < 200) return 25;
        if ($difference < 500) return 45;
        return 80;
    }

    /**
     * Calculate distance between different regions
     */
    private function calculateCrossRegionDistance(int $fromRegion, int $toRegion): int
    {
        $regionDifference = abs($fromRegion - $toRegion);
        
        if ($regionDifference === 1) return 120; // Adjacent regions
        if ($regionDifference === 2) return 200; // 2 regions apart
        if ($regionDifference <= 4) return 350; // 3-4 regions apart
        
        return 500; // Far regions (e.g., Hamburg to Munich)
    }

    /**
     * Clean and validate postal code
     */
    private function cleanPostalCode(string $postalCode): ?string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $postalCode);
        
        if (strlen($cleaned) === 5 && $cleaned >= '01000' && $cleaned <= '99999') {
            return $cleaned;
        }
        
        return null;
    }

    /**
     * Check if postal code is in service area
     */
    public function isInServiceArea(string $postalCode): bool
    {
        $serviceAreas = \App\Models\Setting::getValue('service_areas', []);
        
        if (empty($serviceAreas)) {
            return true; // No restrictions if not configured
        }

        $cleanCode = $this->cleanPostalCode($postalCode);
        
        if (!$cleanCode) {
            return false;
        }

        // Check exact matches first
        if (in_array($cleanCode, $serviceAreas)) {
            return true;
        }

        // Check prefix matches (e.g., "661*" matches "66111", "66112", etc.)
        foreach ($serviceAreas as $area) {
            if (str_ends_with($area, '*')) {
                $prefix = rtrim($area, '*');
                if (str_starts_with($cleanCode, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get maximum service distance
     */
    public function getMaxServiceDistance(): int
    {
        return \App\Models\Setting::getValue('max_service_distance', 100);
    }
}