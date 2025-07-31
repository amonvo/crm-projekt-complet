@extends('layouts.dashboard')

@section('title', 'Seznam firem - CRM System')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Nadpis a tlačítko pro přidání -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Seznam firem</h1>
        @can('create-companies')
            <a href="{{ route('companies.create') }}" class="btn-primary">
                ➕ Přidat firmu
            </a>
        @endcan
    </div>

    <!-- Flash zprávy -->
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- Seznam firem -->
    <div class="card">
        @if($companies->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Název</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kontakt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hodnota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kontakty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Akce</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($companies as $company)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $company->name }}
                                    </div>
                                    @if($company->industry)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $company->industry }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($company->email)
                                        <div>{{ $company->email }}</div>
                                    @endif
                                    @if($company->phone)
                                        <div class="text-gray-500 dark:text-gray-400">{{ $company->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($company->status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                        @elseif($company->status === 'inactive') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                        @else bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 @endif">
                                        @if($company->status === 'active') Aktivní
                                        @elseif($company->status === 'inactive') Neaktivní
                                        @else Prospect @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($company->value)
                                        {{ number_format($company->value, 0, ',', ' ') }} Kč
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $company->contacts->count() }} kontaktů
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('companies.show', $company) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Zobrazit</a>
                                    @can('edit-companies')
                                        <a href="{{ route('companies.edit', $company) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Upravit</a>
                                    @endcan
                                    @can('delete-companies')
                                        <form method="POST" action="{{ route('companies.destroy', $company) }}" class="inline" 
                                              onsubmit="return confirm('Opravdu chcete smazat firmu {{ $company->name }}?')">
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
                {{ $companies->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 dark:text-gray-400 text-lg mb-4">Zatím žádné firmy</div>
                @can('create-companies')
                    <a href="{{ route('companies.create') }}" class="btn-primary">
                        Přidat první firmu
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection
