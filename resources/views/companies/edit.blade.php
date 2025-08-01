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
                    <label for="name" class="form-label">
                        Název firmy <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="form-input" 
                           value="{{ old('name', $company->name) }}" 
                           placeholder="Zadejte název firmy">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="industry" class="form-label">
                        Odvětví
                    </label>
                    <input type="text" name="industry" id="industry"
                           class="form-input" 
                           value="{{ old('industry', $company->industry) }}" 
                           placeholder="např. IT, Výroba, Služby">
                    @error('industry')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kategorie firmy s výrazným označením -->
            <div>
                <label class="form-label">
                    Kategorie firmy <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach(\App\Models\Company::getAvailableCategories() as $key => $category)
                        <div class="relative">
                            <input type="radio" 
                                   name="category" 
                                   id="category_{{ $key }}" 
                                   value="{{ $key }}" 
                                   class="sr-only category-radio"
                                   {{ old('category', $company->category) === $key ? 'checked' : '' }}
                                   onchange="updateCategorySelection(this)">
                            
                            <label for="category_{{ $key }}" 
                                   class="category-card cursor-pointer block relative p-4 border-2 border-gray-200 rounded-lg 
                                          transition-all duration-300 hover:shadow-lg hover:scale-105 hover:border-gray-300
                                          focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-opacity-50">
                                
                                <!-- Ikona a obsah -->
                                <div class="flex flex-col items-center text-center space-y-2">
                                    <span class="text-3xl">{{ $category['icon'] }}</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $category['name'] }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 leading-tight">
                                        {{ $category['description'] }}
                                    </span>
                                </div>
                                
                                <!-- Checkmark indikátor -->
                                <div class="checkmark absolute top-2 right-2 w-6 h-6 bg-green-500 text-white rounded-full 
                                            flex items-center justify-center opacity-0 scale-0 transition-all duration-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('category')
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
                           value="{{ old('email', $company->email) }}" 
                           placeholder="firma@example.com">
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
                           value="{{ old('phone', $company->phone) }}" 
                           placeholder="+420 123 456 789">
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Adresa a webové stránky -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="address" class="form-label">
                        Adresa
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="form-input resize-none" 
                              placeholder="Ulice, město, PSČ">{{ old('address', $company->address) }}</textarea>
                    @error('address')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="website" class="form-label">
                        Webové stránky
                    </label>
                    <input type="url" name="website" id="website"
                           class="form-input" 
                           value="{{ old('website', $company->website) }}" 
                           placeholder="https://www.firma.cz">
                    @error('website')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status a hodnota -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="form-label">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required class="form-input">
                        <option value="prospect" {{ old('status', $company->status) === 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="active" {{ old('status', $company->status) === 'active' ? 'selected' : '' }}>Aktivní</option>
                        <option value="inactive" {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>Neaktivní</option>
                    </select>
                    @error('status')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="value" class="form-label">
                        Hodnota obchodu (Kč)
                    </label>
                    <input type="number" name="value" id="value" min="0" step="0.01"
                           class="form-input" 
                           value="{{ old('value', $company->value) }}" 
                           placeholder="0.00">
                    @error('value')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Poznámky -->
            <div>
                <label for="notes" class="form-label">
                    Poznámky
                </label>
                <textarea name="notes" id="notes" rows="4"
                          class="form-input resize-none" 
                          placeholder="Dodatečné informace o firmě...">{{ old('notes', $company->notes) }}</textarea>
                @error('notes')
                    <p class="form-error">{{ $message }}</p>
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

<style>
/* CSS pro výrazné označení vybrané kategorie */
.category-radio:checked + .category-card {
    border-color: var(--primary-500, #3b82f6);
    background-color: var(--primary-50, #eff6ff);
    border-width: 3px;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.category-radio:checked + .category-card .checkmark {
    opacity: 1;
    scale: 1;
}

.category-radio:checked + .category-card {
    animation: selectedPulse 0.3s ease-out;
}

@keyframes selectedPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1.02); }
}
</style>

<script>
function updateCategorySelection(selectedInput) {
    // Remove selection from all cards
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    const selectedCard = selectedInput.nextElementSibling;
    selectedCard.classList.add('selected');
    
    console.log('Category selected:', selectedInput.value);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedInput = document.querySelector('.category-radio:checked');
    if (checkedInput) {
        updateCategorySelection(checkedInput);
    }
});
</script>
@endsection
