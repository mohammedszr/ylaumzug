<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Check if calculator is enabled
     */
    public function isCalculatorEnabled(): JsonResponse
    {
        try {
            $enabled = Setting::getValue('calculator_enabled', true);
            
            return response()->json([
                'success' => true,
                'enabled' => $enabled
            ]);

        } catch (\Exception $e) {
            \Log::error('Calculator enabled check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'enabled' => true // Default to enabled if error
            ]);
        }
    }

    /**
     * Get all settings (admin only)
     */
    public function index(): JsonResponse
    {
        try {
            $settings = Setting::all()->pluck('value', 'key');
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);

        } catch (\Exception $e) {
            \Log::error('Get settings error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Einstellungen'
            ], 500);
        }
    }

    /**
     * Update settings (admin only)
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $settings = $request->input('settings', []);
            
            foreach ($settings as $key => $value) {
                Setting::setValue($key, $value);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Einstellungen erfolgreich aktualisiert'
            ]);

        } catch (\Exception $e) {
            \Log::error('Update settings error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren der Einstellungen'
            ], 500);
        }
    }
}