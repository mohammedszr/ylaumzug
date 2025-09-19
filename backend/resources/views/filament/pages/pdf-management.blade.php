<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">PDF Verwaltung</h2>
                    <p class="text-sm text-gray-600">Verwalten Sie PDF-Angebote und Speicherplatz</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-900">PDF Dateien</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['file_count'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Gesamtgröße</p>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['total_size_mb'] ?? 0 }} MB</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-900">Durchschnittsgröße</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $stats['average_size_kb'] ?? 0 }} KB</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-900">Status</p>
                            <p class="text-sm font-bold text-yellow-600">
                                @if(isset($stats['error']))
                                    Fehler
                                @else
                                    Aktiv
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">PDF-System Funktionen</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">✅ Automatische PDF-Generierung</h4>
                        <p class="text-sm text-gray-600">PDFs werden automatisch für Angebote erstellt und können per E-Mail versendet werden.</p>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">📧 E-Mail Integration</h4>
                        <p class="text-sm text-gray-600">PDFs werden automatisch als Anhang zu E-Mails hinzugefügt.</p>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">👁️ Vorschau-Funktion</h4>
                        <p class="text-sm text-gray-600">Administratoren können PDFs vor dem Versand in der Vorschau anzeigen.</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">💾 Speicher-Management</h4>
                        <p class="text-sm text-gray-600">Automatische Bereinigung alter PDF-Dateien zur Speicherplatz-Optimierung.</p>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">🎨 Professionelles Design</h4>
                        <p class="text-sm text-gray-600">PDFs enthalten Firmenbranding und professionelle Formatierung.</p>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">📊 Detaillierte Preisaufschlüsselung</h4>
                        <p class="text-sm text-gray-600">Vollständige Kostenaufstellung mit allen Serviceleistungen.</p>
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
                    <h3 class="text-sm font-medium text-red-800">PDF-System Fehler</h3>
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
                    <h3 class="text-sm font-medium text-blue-800">Wartungshinweise</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>PDFs werden automatisch nach 90 Tagen gelöscht (konfigurierbar)</li>
                            <li>Regelmäßige Bereinigung empfohlen bei hohem Aufkommen</li>
                            <li>PDF-Generierung benötigt ausreichend Speicherplatz</li>
                            <li>Bei Problemen prüfen Sie die Laravel-Logs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>