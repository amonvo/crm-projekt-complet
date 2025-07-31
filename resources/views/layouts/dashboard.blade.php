@extends('layouts.app')

@section('content')
<!-- Navigation -->
<nav class="bg-white dark:bg-gray-800 shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">CRM System</h1>
                <div class="ml-10 flex space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-gray-900 dark:text-white hover:text-blue-600 px-3 py-2 rounded-md">Dashboard</a>
                    <a href="{{ route('companies.index') }}" class="text-gray-900 dark:text-white hover:text-blue-600 px-3 py-2 rounded-md">Firmy</a>
                    <a href="{{ route('contacts.index') }}" class="text-gray-900 dark:text-white hover:text-blue-600 px-3 py-2 rounded-md">Kontakty</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button @click="darkMode = !darkMode" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span x-show="!darkMode">🌙</span>
                    <span x-show="darkMode">☀️</span>
                </button>
                <span class="text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary">Odhlásit</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="max-w-7xl mx-auto py-6 px-4">
    @yield('dashboard-content')
</main>
@endsection
