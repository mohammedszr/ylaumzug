<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YLA Umzug - Settings Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <h1 class="text-3xl font-bold text-gray-900">YLA Umzug Admin</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Calculator Toggle -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">Calculator:</span>
                            <button 
                                id="calculator-toggle"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ Setting::getValue('calculator_enabled', true) ? 'bg-indigo-600' : 'bg-gray-200' }}"
                                onclick="toggleCalculator()"
                            >
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ Setting::getValue('calculator_enabled', true) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                            <span id="calculator-status" class="text-sm font-medium {{ Setting::getValue('calculator_enabled', true) ? 'text-green-600' : 'text-red-600' }}">
                                {{ Setting::getValue('calculator_enabled', true) ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Settings Form -->
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
                @csrf
                @method('PUT')

                @foreach($settings as $groupKey => $group)
                    @if($group['settings']->count() > 0)
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    {{ $group['title'] }}
                                </h3>
                                
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    @foreach($group['settings'] as $setting)
                                        <div>
                                            <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700">
                                                {{ $setting->description ?: $setting->key }}
                                            </label>
                                            
                                            @if($setting->type === 'boolean')
                                                <select name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="1" {{ $setting->value === '1' ? 'selected' : '' }}>Yes</option>
                                                    <option value="0" {{ $setting->value === '0' ? 'selected' : '' }}>No</option>
                                                </select>
                                            @elseif($setting->type === 'json' || $setting->type === 'array')
                                                <textarea 
                                                    name="settings[{{ $setting->key }}]" 
                                                    id="{{ $setting->key }}" 
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    placeholder="JSON format"
                                                >{{ $setting->value }}</textarea>
                                            @else
                                                <input 
                                                    type="{{ $setting->type === 'decimal' || $setting->type === 'float' ? 'number' : ($setting->type === 'integer' ? 'number' : 'text') }}" 
                                                    name="settings[{{ $setting->key }}]" 
                                                    id="{{ $setting->key }}" 
                                                    value="{{ $setting->value }}"
                                                    @if($setting->type === 'decimal' || $setting->type === 'float') step="0.01" @endif
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                >
                                            @endif
                                            
                                            @if($setting->key)
                                                <p class="mt-1 text-xs text-gray-500">Key: {{ $setting->key }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save All Settings
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // CSRF token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function toggleCalculator() {
            const toggle = document.getElementById('calculator-toggle');
            const status = document.getElementById('calculator-status');
            const isEnabled = toggle.classList.contains('bg-indigo-600');
            
            try {
                const response = await fetch('{{ route("admin.settings.toggle-calculator") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        enabled: !isEnabled
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update toggle appearance
                    if (data.enabled) {
                        toggle.classList.remove('bg-gray-200');
                        toggle.classList.add('bg-indigo-600');
                        toggle.querySelector('span').classList.remove('translate-x-1');
                        toggle.querySelector('span').classList.add('translate-x-6');
                        status.textContent = 'Enabled';
                        status.classList.remove('text-red-600');
                        status.classList.add('text-green-600');
                    } else {
                        toggle.classList.remove('bg-indigo-600');
                        toggle.classList.add('bg-gray-200');
                        toggle.querySelector('span').classList.remove('translate-x-6');
                        toggle.querySelector('span').classList.add('translate-x-1');
                        status.textContent = 'Disabled';
                        status.classList.remove('text-green-600');
                        status.classList.add('text-red-600');
                    }
                }
            } catch (error) {
                console.error('Error toggling calculator:', error);
                alert('Error updating calculator status');
            }
        }
    </script>
</body>
</html>