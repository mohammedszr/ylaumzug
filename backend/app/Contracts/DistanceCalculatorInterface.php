<?php

namespace App\Contracts;

interface DistanceCalculatorInterface
{
    public function calculateDistance(string $fromPostalCode, string $toPostalCode): array;
    public function geocodePostalCode(string $postalCode): array;
}