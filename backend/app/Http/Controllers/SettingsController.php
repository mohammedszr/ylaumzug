<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Check if calculator is enabled
     */
    public function isCalculatorEnabled(): JsonResponse
    {
        $enabled = Setting::getValue('calculator_enabled', true);
        
        return response()->json([
            'success' => true,
            'enabled' => $enabled
        ]);
    }

    /**
     * Get public settings for frontend
     */
    public function getPublicSettings(): JsonResponse
    {
        $settings = Setting::getPublicSettings();
        
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }
}