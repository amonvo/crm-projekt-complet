@extends('layouts.dashboard')

@section('title', 'Upravit firmu - CRM System')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Nadpis s navigací -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Upravit firmu</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $company->name }}</p>
        </div>
        <a href="{{ route('companies.index') }}" class="btn-secondary">
            ← Zpět na seznam
        </a>
    </div>

    <!-- Formulář -->
    <div class="card">
        <form method="POST" action="{{ route('companies.update', $company) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Základní informace -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Název firmy <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="form-input w-full" 
                           value="{{ old('name', $company->name) }}" 
                           placeholder="Zadejte název firmy">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="industry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Odvětví
                    </label>
                    <input type="text" name="industry" id="industry"
                           class="form-input w-full" 
                           value="{{ old('industry', $company->industry) }}" 
                           placeholder="např. IT, Výroba, Služby">
                    @error('industry')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kontaktní informace -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email
                    </label>
                    <input type="email" name="email" id="email"
                           class="form-input w-full" 
                           value="{{ old('email', $company->email) }}" 
                           placeholder="firma@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Telefon
                    </label>
                    <input type="text" name="phone" id="phone"
                           class="form-input w-full" 
                           value="{{ old('phone', $company->phone) }}" 
                           placeholder="+420 123 456 789">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Adresa a webové stránky -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Adresa
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="form-input w-full resize-none" 
                              placeholder="Ulice, město, PSČ">{{ old('address', $company->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Webové stránky
                    </label>
                    <input type="url" name="website" id="website"
                           class="form-input w-full" 
                           value="{{ old('website', $company->website) }}" 
                           placeholder="https://www.firma.cz">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status a hodnota -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required class="form-input w-full">
                        <option value="prospect" {{ old('status', $company->status) === 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="active" {{ old('status', $company->status) === 'active' ? 'selected' : '' }}>Aktivní</option>
                        <option value="inactive" {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>Neaktivní</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Hodnota obchodu (Kč)
                    </label>
                    <input type="number" name="value" id="value" min="0" step="0.01"
                           class="form-input w-full" 
                           value="{{ old('value', $company->value) }}" 
                           placeholder="0.00">
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Poznámky -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Poznámky
                </label>
                <textarea name="notes" id="notes" rows="4"
                          class="form-input w-full resize-none" 
                          placeholder="Dodatečné informace o firmě...">{{ old('notes', $company->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tlačítka -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('companies.index') }}" class="btn-secondary">
                    Zrušit
                </a>
                <button type="submit" class="btn-primary">
                    💾 Uložit změny
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
