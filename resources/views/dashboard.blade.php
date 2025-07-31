@extends('layouts.dashboard')

@section('title', 'Dashboard - CRM System')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Nadpis -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <span class="text-sm text-gray-600 dark:text-gray-400">
            Vítejte, {{ Auth::user()->name }}! 
            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-xs font-medium">
                {{ Auth::user()->getRoleNames()->first() }}
            </span>
        </span>
    </div>

    <!-- Statistiky -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card">
            <div class="flex items-center">
                <div class="p-2 bg-blue-500 rounded-lg">
                    <span class="text-white text-xl">🏢</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Celkem firem</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['companies_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center">
                <div class="p-2 bg-green-500 rounded-lg">
                    <span class="text-white text-xl">👥</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Celkem kontaktů</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['contacts_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center">
                <div class="p-2 bg-purple-500 rounded-lg">
                    <span class="text-white text-xl">👤</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Celkem uživatelů</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['users_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-500 rounded-lg">
                    <span class="text-white text-xl">✅</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aktivní firmy</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_companies'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Poslední aktivity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Nejnovější firmy -->
        <div class="card">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Nejnovější firmy</h3>
            <div class="space-y-3">
                @forelse($recent_companies as $company)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $company->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $company->email }}</p>
                        </div>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs">
                            {{ $company->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Zatím žádné firmy</p>
                @endforelse
            </div>
            <div class="mt-4">
                <a href="{{ route('companies.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    Zobrazit všechny firmy →
                </a>
            </div>
        </div>

        <!-- Nejnovější kontakty -->
        <div class="card">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Nejnovější kontakty</h3>
            <div class="space-y-3">
                @forelse($recent_contacts as $contact)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $contact->first_name }} {{ $contact->last_name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->company->name ?? 'Bez firmy' }}</p>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $contact->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Zatím žádné kontakty</p>
                @endforelse
            </div>
            <div class="mt-4">
                <a href="{{ route('contacts.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    Zobrazit všechny kontakty →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
