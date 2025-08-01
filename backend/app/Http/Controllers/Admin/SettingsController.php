<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display settings management page
     */
    public function index()
    {
        $settingGroups = [
            'calculator' => 'Calculator Control',
            'business' => 'Business Information',
            'moving' => 'Moving Service Pricing',
            'decluttering' => 'Decluttering Service Pricing',
            'cleaning' => 'Cleaning Service Pricing',
            'discounts' => 'Discounts & Bonuses',
            'surcharges' => 'Surcharges',
            'pricing' => 'General Pricing'
        ];

        $settings = [];
        foreach ($settingGroups as $group => $title) {
            $settings[$group] = [
                'title' => $title,
                'settings' => Setting::where('group', $group)->orderBy('key')->get()
            ];
        }

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required'
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                // Cast value based on type
                $castedValue = $this->castValueForStorage($value, $setting->type);
                
                $setting->update(['value' => $castedValue]);
                
                // Clear cache
                Cache::forget("setting.{$key}");
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Toggle calculator status
     */
    public function toggleCalculator(Request $request)
    {
        $enabled = $request->boolean('enabled');
        
        Setting::setValue('calculator_enabled', $enabled, 'boolean');
        
        $status = $enabled ? 'enabled' : 'disabled';
        
        return response()->json([
            'success' => true,
            'message' => "Calculator {$status} successfully",
            'enabled' => $enabled
        ]);
    }

    /**
     * Get settings for API
     */
    public function apiIndex()
    {
        $settings = Setting::all()->groupBy('group');
        
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Cast value for storage based on type
     */
    private function castValueForStorage(mixed $value, string $type): string
    {
        return match($type) {
            'json', 'array' => is_string($value) ? $value : json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value
        };
    }
}