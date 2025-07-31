@extends('layouts.dashboard')

@section('title', $contact->first_name . ' ' . $contact->last_name . ' - Detail kontaktu - CRM System')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Nadpis s navigací -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $contact->first_name }} {{ $contact->last_name }}
                @if($contact->is_primary)
                    <span class="badge badge-blue ml-2">Hlavní kontakt</span>
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Detail kontaktu</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('contacts.index') }}" class="btn-secondary">
                ← Zpět na seznam
            </a>
            @can('edit-contacts')
                <a href="{{ route('contacts.edit', $contact) }}" class="btn-primary">
                    ✏️ Upravit
                </a>
            @endcan
        </div>
    </div>

    <!-- Informace o kontaktu -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Hlavní informace -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Základní informace</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Křestní jméno</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $contact->first_name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Příjmení</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $contact->last_name }}</dd>
                    </div>
                    
                    @if($contact->position)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pozice</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $contact->position }}</dd>
                        </div>
                    @endif
                    
                    @if($contact->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:text-blue-500">
                                    {{ $contact->email }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    
                    @if($contact->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefon</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="tel:{{ $contact->phone }}" class="text-blue-600 hover:text-blue-500">
                                    {{ $contact->phone }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Firma -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Firma</h3>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            <a href="{{ route('companies.show', $contact->company) }}" class="text-blue-600 hover:text-blue-500">
                                {{ $contact->company->name }}
                            </a>
                        </h4>
                        @if($contact->company->industry)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $contact->company->industry }}</p>
                        @endif
                        <div class="flex space-x-4 mt-2">
                            @if($contact->company->email)
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->company->email }}</span>
                            @endif
                            @if($contact->company->phone)
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->company->phone }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @if($contact->company->status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                            @elseif($contact->company->status === 'inactive') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                            @else bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 @endif">
                            @if($contact->company->status === 'active') Aktivní
                            @elseif($contact->company->status === 'inactive') Neaktivní
                            @else Prospect @endif
                        </span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('companies.show', $contact->company) }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Zobrazit detail firmy →
                    </a>
                </div>
            </div>
        </div>

        <!-- Postranní panel -->
        <div class="space-y-6">
            <!-- Status kontaktu -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Status kontaktu</h3>
                
                <div class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Typ kontaktu</dt>
                        <dd class="mt-1">
                            @if($contact->is_primary)
                                <span class="badge badge-blue">✨ Hlavní kontakt</span>
                            @else
                                <span class="badge bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">👤 Běžný kontakt</span>
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Přidáno</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $contact->created_at->format('d.m.Y H:i') }}
                        </dd>
                    </div>
                    
                    @if($contact->updated_at != $contact->created_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Naposledy upraveno</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $contact->updated_at->format('d.m.Y H:i') }}
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Rychlé akce -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Rychlé akce</h3>
                
                <div class="space-y-2">
                    @if($contact->email)
                        <a href="mailto:{{ $contact->email }}" class="btn-secondary w-full text-center">
                            📧 Poslat email
                        </a>
                    @endif
                    
                    @if($contact->phone)
                        <a href="tel:{{ $contact->phone }}" class="btn-secondary w-full text-center">
                            📞 Zavolat
                        </a>
                    @endif
                    
                    <a href="{{ route('companies.show', $contact->company) }}" class="btn-secondary w-full text-center">
                        🏢 Zobrazit firmu
                    </a>
                </div>
            </div>

            <!-- Akce správy -->
            @canany(['edit-contacts', 'delete-contacts'])
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Správa kontaktu</h3>
                    <div class="space-y-2">
                        @can('edit-contacts')
                            <a href="{{ route('contacts.edit', $contact) }}" class="btn-primary w-full text-center">
                                ✏️ Upravit kontakt
                            </a>
                        @endcan
                        @can('delete-contacts')
                            <form method="POST" action="{{ route('contacts.destroy', $contact) }}" 
                                  onsubmit="return confirm('Opravdu chcete smazat kontakt {{ $contact->first_name }} {{ $contact->last_name }}? Tato akce je nevratná.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full">
                                    🗑️ Smazat kontakt
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
