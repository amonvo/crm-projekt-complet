@extends('layouts.dashboard')

@section('title', 'Přidat kontakt - CRM System')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Nadpis s navigací -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Přidat nový kontakt</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Vyplňte informace o novém kontaktu</p>
        </div>
        <a href="{{ route('contacts.index') }}" class="btn-secondary">
            ← Zpět na seznam
        </a>
    </div>

    <!-- Formulář -->
    <div class="card">
        <form method="POST" action="{{ route('contacts.store') }}" class="space-y-6">
            @csrf

            <!-- Firma -->
            <div>
                <label for="company_id" class="form-label">
                    Firma <span class="text-red-500">*</span>
                </label>
                <select name="company_id" id="company_id" required class="form-input">
                    <option value="">Vyberte firmu</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', request('company')) == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jméno a příjmení -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="form-label">
                        Křestní jméno <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" required
                           class="form-input" 
                           value="{{ old('first_name') }}" 
                           placeholder="Jan">
                    @error('first_name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="form-label">
                        Příjmení <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" required
                           class="form-input" 
                           value="{{ old('last_name') }}" 
                           placeholder="Novák">
                    @error('last_name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pozice -->
            <div>
                <label for="position" class="form-label">
                    Pozice ve firmě
                </label>
                <input type="text" name="position" id="position"
                       class="form-input" 
                       value="{{ old('position') }}" 
                       placeholder="např. Ředitel, Projektový manažer">
                @error('position')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kontaktní informace -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="form-label">
                        Email
                    </label>
                    <input type="email" name="email" id="email"
                           class="form-input" 
                           value="{{ old('email') }}" 
                           placeholder="jan.novak@firma.cz">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="form-label">
                        Telefon
                    </label>
                    <input type="text" name="phone" id="phone"
                           class="form-input" 
                           value="{{ old('phone') }}" 
                           placeholder="+420 123 456 789">
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hlavní kontakt -->
            <div class="flex items-center">
                <input type="checkbox" name="is_primary" id="is_primary" value="1"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       {{ old('is_primary') ? 'checked' : '' }}>
                <label for="is_primary" class="ml-2 block text-sm text-gray-900 dark:text-white">
                    Označit jako hlavní kontakt firmy
                </label>
                @error('is_primary')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Hlavní kontakt se zobrazuje jako první a je zvýrazněný v seznamech.
            </p>

            <!-- Tlačítka -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('contacts.index') }}" class="btn-secondary">
                    Zrušit
                </a>
                <button type="submit" class="btn-primary">
                    💾 Uložit kontakt
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
