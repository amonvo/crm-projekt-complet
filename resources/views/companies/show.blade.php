@extends('layouts.dashboard')

@section('title', $company->name . ' - Detail firmy - CRM System')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Nadpis s navigací -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $company->name }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Detail firmy</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('companies.index') }}" class="btn-secondary">
                ← Zpět na seznam
            </a>
            @can('edit-companies')
                <a href="{{ route('companies.edit', $company) }}" class="btn-primary">
                    ✏️ Upravit
                </a>
            @endcan
        </div>
    </div>

    <!-- Informace o firmě -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Hlavní informace -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Základní informace</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Název</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->name }}</dd>
                    </div>
                    
                    @if($company->industry)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Odvětví</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->industry }}</dd>
                        </div>
                    @endif
                    
                    @if($company->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="mailto:{{ $company->email }}" class="text-blue-600 hover:text-blue-500">
                                    {{ $company->email }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    
                    @if($company->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefon</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="tel:{{ $company->phone }}" class="text-blue-600 hover:text-blue-500">
                                    {{ $company->phone }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    
                    @if($company->website)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Webové stránky</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-500">
                                    {{ $company->website }} ↗
                                </a>
                            </dd>
                        </div>
                    @endif
                    
                    @if($company->address)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Adresa</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $company->address }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kontakty firmy -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Kontakty ({{ $company->contacts->count() }})</h3>
                    @can('create-contacts')
                        <a href="{{ route('contacts.create', ['company' => $company->id]) }}" class="btn-secondary text-sm">
                            ➕ Přidat kontakt
                        </a>
                    @endcan
                </div>

                @if($company->contacts->count() > 0)
                    <div class="space-y-3">
                        @foreach($company->contacts as $contact)
                            <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            {{ $contact->first_name }} {{ $contact->last_name }}
                                        </h4>
                                        @if($contact->is_primary)
                                            <span class="ml-2 px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">
                                                Hlavní kontakt
                                            </span>
                                        @endif
                                    </div>
                                    @if($contact->position)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $contact->position }}</p>
                                    @endif
                                    <div class="flex space-x-4 mt-2">
                                        @if($contact->email)
                                            <a href="mailto:{{ $contact->email }}" class="text-sm text-blue-600 hover:text-blue-500">
                                                {{ $contact->email }}
                                            </a>
                                        @endif
                                        @if($contact->phone)
                                            <a href="tel:{{ $contact->phone }}" class="text-sm text-blue-600 hover:text-blue-500">
                                                {{ $contact->phone }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-blue-600 hover:text-blue-500 text-sm">Zobrazit</a>
                                    @can('edit-contacts')
                                        <a href="{{ route('contacts.edit', $contact) }}" class="text-indigo-600 hover:text-indigo-500 text-sm">Upravit</a>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Zatím žádné kontakty</p>
                        @can('create-contacts')
                            <a href="{{ route('contacts.create', ['company' => $company->id]) }}" class="btn-secondary mt-2">
                                Přidat první kontakt
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Postranní panel -->
        <div class="space-y-6">
            <!-- Status a hodnota -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Status</h3>
                
                <div class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Současný status</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                @if($company->status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                @elseif($company->status === 'inactive') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                @else bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 @endif">
                                @if($company->status === 'active') ✅ Aktivní
                                @elseif($company->status === 'inactive') ❌ Neaktivní
                                @else ⏳ Prospect @endif
                            </span>
                        </dd>
                    </div>
                    
                    @if($company->value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hodnota obchodu</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                {{ number_format($company->value, 0, ',', ' ') }} Kč
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Poznámky -->
            @if($company->notes)
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Poznámky</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $company->notes }}</p>
                </div>
            @endif

            <!-- Akce -->
            @canany(['edit-companies', 'delete-companies'])
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Akce</h3>
                    <div class="space-y-2">
                        @can('edit-companies')
                            <a href="{{ route('companies.edit', $company) }}" class="btn-primary w-full text-center">
                                ✏️ Upravit firmu
                            </a>
                        @endcan
                        @can('delete-companies')
                            <form method="POST" action="{{ route('companies.destroy', $company) }}" 
                                  onsubmit="return confirm('Opravdu chcete smazat firmu {{ $company->name }}? Tato akce je nevratná.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full">
                                    🗑️ Smazat firmu
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endcanany
        </div>
    </div>
</div>
@endsection
