<!DOCTYPE html>
<html lang="cs" 
      x-data="{ 
          darkMode: JSON.parse(localStorage.getItem('darkMode') || 'false')
      }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', JSON.stringify(val)))" 
      :class="{ 'dark': darkMode }"
      data-theme="blue">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    
    <!-- Navigation s theme switcherem -->
    <nav class="bg-white dark:bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <!-- Logo a navigace -->
                <div class="flex items-center flex-1">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        <span class="text-primary">CRM</span> System
                    </h1>
                    <div class="ml-10 flex space-x-8">
                        <a href="{{ route('dashboard') }}" 
                           class="text-gray-900 dark:text-white hover:text-primary px-3 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-primary font-semibold' : '' }}">
                            📊 Dashboard
                        </a>
                        <a href="{{ route('companies.index') }}" 
                           class="text-gray-900 dark:text-white hover:text-primary px-3 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('companies.*') ? 'text-primary font-semibold' : '' }}">
                            🏢 Firmy
                        </a>
                        <a href="{{ route('contacts.index') }}" 
                           class="text-gray-900 dark:text-white hover:text-primary px-3 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('contacts.*') ? 'text-primary font-semibold' : '' }}">
                            👥 Kontakty
                        </a>
                    </div>
                    
                    <!-- Live Search -->
                    <div class="ml-8 flex-1 max-w-lg">
                        @include('components.live-search')
                    </div>
                </div>
                
                <!-- Pravá strana - Theme switcher, Dark mode, User menu -->
                <div class="flex items-center space-x-4">
                    <!-- Theme Switcher -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200">
                            <div class="w-4 h-4 rounded-full bg-blue-500" id="theme-indicator"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300" id="theme-name">Modrá</span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Theme Dropdown -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-1 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-1 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                            
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Vyberte téma</h3>
                            </div>
                            
                            <div class="p-2 space-y-1">
                                <button onclick="changeThemeSimple('blue')" 
                                       class="w-full flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                                    <span class="text-sm text-gray-900 dark:text-white">Modrá</span>
                                    <div class="ml-auto text-blue-500 theme-check" id="check-blue" style="display: block;">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </button>
                                
                                <button onclick="changeThemeSimple('green')"
                                       class="w-full flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="w-4 h-4 rounded-full bg-green-500"></div>
                                    <span class="text-sm text-gray-900 dark:text-white">Zelená</span>
                                    <div class="ml-auto text-green-500 theme-check" id="check-green" style="display: none;">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </button>
                                
                                <button onclick="changeThemeSimple('purple')"
                                       class="w-full flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="w-4 h-4 rounded-full bg-purple-500"></div>
                                    <span class="text-sm text-gray-900 dark:text-white">Fialová</span>
                                    <div class="ml-auto text-purple-500 theme-check" id="check-purple" style="display: none;">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </button>
                                
                                <button onclick="changeThemeSimple('orange')"
                                       class="w-full flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="w-4 h-4 rounded-full bg-orange-500"></div>
                                    <span class="text-sm text-gray-900 dark:text-white">Oranžová</span>
                                    <div class="ml-auto text-orange-500 theme-check" id="check-orange" style="display: none;">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </button>
                                
                                <button onclick="changeThemeSimple('red')"
                                       class="w-full flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="w-4 h-4 rounded-full bg-red-500"></div>
                                    <span class="text-sm text-gray-900 dark:text-white">Červená</span>
                                    <div class="ml-auto text-red-500 theme-check" id="check-red" style="display: none;">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dark/Light Mode Toggle -->
                    <button @click="darkMode = !darkMode" 
                            class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 hover:scale-110">
                        <span x-show="!darkMode" class="text-xl">🌙</span>
                        <span x-show="darkMode" class="text-xl">☀️</span>
                    </button>
                    
                    <!-- User Info -->
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ Auth::user()->getRoleNames()->first() }}
                            </div>
                        </div>
                        
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="btn-secondary text-sm px-3 py-1 hover:scale-105 transition-transform duration-200">
                                Odhlásit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Toast Notification -->
    <div id="toast-notification" class="fixed top-4 right-4 z-50 transform translate-x-full opacity-0 transition-all duration-300">
        <div class="px-4 py-3 rounded-lg shadow-lg border bg-green-100 border-green-200 text-green-800">
            <div class="flex items-center space-x-2">
                <span class="text-green-500">✅</span>
                <span id="toast-message" class="text-sm font-medium"></span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 fade-in">
        @yield('dashboard-content')
    </main>

    <script>
    // Theme configuration
    const themes = {
        blue: { name: 'Modrá', color: 'bg-blue-500' },
        green: { name: 'Zelená', color: 'bg-green-500' },
        purple: { name: 'Fialová', color: 'bg-purple-500' },
        orange: { name: 'Oranžová', color: 'bg-orange-500' },
        red: { name: 'Červená', color: 'bg-red-500' }
    };

    let currentTheme = '{{ Auth::user()->preferred_theme }}' || 'blue';

    // Initialize theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateThemeUI(currentTheme);
        document.documentElement.setAttribute('data-theme', currentTheme);
    });

    function changeThemeSimple(theme) {
        console.log('Changing theme to:', theme);
        
        // Okamžitá změna atributu a UI
        document.documentElement.setAttribute('data-theme', theme);
        updateThemeUI(theme);
        currentTheme = theme;
        
        // Uložit do localStorage
        localStorage.setItem('selectedTheme', theme);
        
        // Zobrazit success zprávu
        showToast('Téma bylo změněno na: ' + themes[theme].name);
        
        // Volitelně: pošli na server na pozadí (bez čekání na response)
        fetch('/theme/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ theme: theme })
        }).catch(error => {
            console.log('Server update failed, but theme changed locally:', error);
        });
    }

    function updateThemeUI(theme) {
        // Update theme indicator
        const indicator = document.getElementById('theme-indicator');
        const themeName = document.getElementById('theme-name');
        
        if (indicator && themeName) {
            // Remove all theme classes
            indicator.className = 'w-4 h-4 rounded-full';
            indicator.classList.add(themes[theme].color);
            themeName.textContent = themes[theme].name;
        }
        
        // Update checkmarks
        document.querySelectorAll('.theme-check').forEach(check => {
            check.style.display = 'none';
        });
        
        const activeCheck = document.getElementById('check-' + theme);
        if (activeCheck) {
            activeCheck.style.display = 'block';
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-notification');
        const toastMessage = document.getElementById('toast-message');
        
        if (toast && toastMessage) {
            toastMessage.textContent = message;
            
            // Změnit barvy podle typu
            const toastDiv = toast.querySelector('div');
            toastDiv.className = 'px-4 py-3 rounded-lg shadow-lg border';
            
            if (type === 'success') {
                toastDiv.classList.add('bg-green-100', 'border-green-200', 'text-green-800');
            } else if (type === 'error') {
                toastDiv.classList.add('bg-red-100', 'border-red-200', 'text-red-800');
            } else if (type === 'warning') {
                toastDiv.classList.add('bg-yellow-100', 'border-yellow-200', 'text-yellow-800');
            }
            
            // Show toast
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
            
            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                toast.classList.remove('translate-x-0', 'opacity-100');
            }, 3000);
        }
    }
    </script>

</body>
</html>
