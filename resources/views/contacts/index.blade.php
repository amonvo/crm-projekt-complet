@extends('layouts.dashboard')

@section('title', 'Seznam kontaktů - CRM System')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Nadpis a tlačítko pro přidání -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Seznam kontaktů</h1>
        @can('create-contacts')
            <a href="{{ route('contacts.create') }}" class="btn-primary">
                ➕ Přidat kontakt
            </a>
        @endcan
    </div>

    <!-- Flash zprávy -->
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- Seznam kontaktů -->
    <div class="card">
        @if($contacts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jméno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Firma</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pozice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kontakt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Akce</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($contacts as $contact)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $contact->first_name }} {{ $contact->last_name }}
                                            </div>
                                            @if($contact->is_primary)
                                                <span class="badge badge-blue">Hlavní kontakt</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <a href="{{ route('companies.show', $contact->company) }}" class="text-blue-600 hover:text-blue-500">
                                        {{ $contact->company->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $contact->position ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($contact->email)
                                        <div><a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:text-blue-500">{{ $contact->email }}</a></div>
                                    @endif
                                    @if($contact->phone)
                                        <div class="text-gray-500 dark:text-gray-400"><a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Přidáno {{ $contact->created_at->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Zobrazit</a>
                                    @can('edit-contacts')
                                        <a href="{{ route('contacts.edit', $contact) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Upravit</a>
                                    @endcan
                                    @can('delete-contacts')
                                        <form method="POST" action="{{ route('contacts.destroy', $contact) }}" class="inline" 
                                              onsubmit="return confirm('Opravdu chcete smazat kontakt {{ $contact->first_name }} {{ $contact->last_name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Smazat</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginace -->
            <div class="mt-4">
                {{ $contacts->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 dark:text-gray-400 text-lg mb-4">Zatím žádné kontakty</div>
                @can('create-contacts')
                    <a href="{{ route('contacts.create') }}" class="btn-primary">
                        Přidat první kontakt
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection
