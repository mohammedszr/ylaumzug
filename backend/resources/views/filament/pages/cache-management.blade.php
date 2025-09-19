<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Cache Verwaltung</h2>
                    <p class="text-sm text-gray-600">Verwalten Sie Cache-Daten für optimale Performance</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-900">Cache Treiber</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['driver'] ?? 'Unbekannt' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if(($stats['status'] ?? 'error') === 'connected')
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Status</p>
                            <p class="text-sm font-bold {{ ($stats['status'] ?? 'error') === 'connected' ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($stats['status'] ?? 'error') === 'connected' ? 'Verbunden' : 'Fehler' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-900">Letzter Check</p>
                            <p class="text-sm font-bold text-purple-600">{{ now()->format('H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cache-Typen</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">⚙️ Einstellungen Cache</h4>
                        <p class="text-sm text-gray-600 mb-3">Zwischenspeicherung von Anwendungseinstellungen für bessere Performance.</p>
                        <div class="text-xs text-gray-500">
                            <p>• Gültigkeitsdauer: 1 Stunde</p>
                            <p>• Automatische Invalidierung bei Änderungen</p>
                        </div>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">📍 Entfernungs Cache</h4>
                        <p class="text-sm text-gray-600 mb-3">Zwischenspeicherung von Entfernungsberechnungen zwischen Postleitzahlen.</p>
                        <div class="text-xs text-gray-500">
                            <p>• Gültigkeitsdauer: 1 Stunde</p>
                            <p>• Reduziert API-Aufrufe an OpenRouteService</p>
                        </div>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">💰 Preis Cache</h4>
                        <p class="text-sm text-gray-600 mb-3">Zwischenspeicherung von Preisberechnungen für häufige Anfragen.</p>
                        <div class="text-xs text-gray-500">
                            <p>• Gültigkeitsdauer: 30 Minuten</p>
                            <p>• Basiert auf Service-Kombinationen</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">🔧 Services Cache</h4>
                        <p class="text-sm text-gray-600 mb-3">Zwischenspeicherung der verfügbaren Services und deren Konfiguration.</p>
                        <div class="text-xs text-gray-500">
                            <p>• Gültigkeitsdauer: 2 Stunden</p>
                            <p>• Automatische Invalidierung bei Service-Änderungen</p>
                        </div>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">🌐 API Response Cache</h4>
                        <p class="text-sm text-gray-600 mb-3">Zwischenspeicherung von API-Antworten für statische Daten.</p>
                        <div class="text-xs text-gray-500">
                            <p>• Gültigkeitsdauer: 5 Minuten</p>
                            <p>• Reduziert Datenbankabfragen</p>
                        </div>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">👤 Benutzer Cache</h4>
                        <p class="text-sm text-gray-600 mb-3">Zwischenspeicherung von Benutzersitzungen und -präferenzen.</p>
                        <div class="text-xs text-gray-500">
                            <p>• Gültigkeitsdauer: 24 Stunden</p>
                            <p>• Verbessert Anmelde-Performance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($stats['error']))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Cache-System Fehler</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $stats['error'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Performance-Tipps</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Verwenden Sie "Anwendung optimieren" nach größeren Änderungen</li>
                            <li>Leeren Sie spezifische Caches nur bei Bedarf</li>
                            <li>Redis bietet bessere Performance als File-Cache</li>
                            <li>Überwachen Sie die Cache-Hit-Rate für Optimierungen</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>