<div x-data="liveSearch()" class="relative">
    <!-- Search Input -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input 
            x-model="query"
            @input.debounce.300ms="search()"
            @focus="showResults = true"
            @keydown.escape="showResults = false"
            @keydown.arrow-down.prevent="selectNext()"
            @keydown.arrow-up.prevent="selectPrevious()"
            @keydown.enter.prevent="selectCurrent()"
            type="text" 
            class="form-input pl-10 pr-10 w-full"
            placeholder="Vyhledat firmy a kontakty..."
            autocomplete="off">
        
        <!-- Loading Spinner -->
        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <div class="loading-spinner"></div>
        </div>
        
        <!-- Clear Button -->
        <div x-show="query.length > 0 && !loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <button @click="clearSearch()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Search Results Dropdown -->
    <div x-show="showResults && (results.companies.length > 0 || results.contacts.length > 0 || query.length >= 2)"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-1 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-1 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showResults = false"
         class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto">
        
        <!-- Companies Results -->
        <template x-if="results.companies && results.companies.length > 0">
            <div>
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 border-b">
                    🏢 Firmy (<span x-text="results.companies.length"></span>)
                </div>
                <template x-for="(company, index) in results.companies" :key="company.id">
                    <a :href="company.url" 
                       :class="{ 'bg-gray-50 dark:bg-gray-700': selectedIndex === index }"
                       class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 transition-colors duration-150">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-lg"
                                 :style="{ backgroundColor: getColorForCategory(company.category) + '20' }">
                                <span x-text="company.icon"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-html="company.highlighted"></p>
                                <div class="flex items-center space-x-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="company.subtitle"></p>
                                    <span :class="getStatusBadgeClass(company.status)" class="px-2 py-0.5 text-xs rounded-full">
                                        <span x-text="getStatusText(company.status)"></span>
                                    </span>
                                </div>
                                <p x-show="company.description" class="text-xs text-gray-400 dark:text-gray-500 truncate" x-text="company.description"></p>
                            </div>
                            <div class="text-right">
                                <p x-show="company.contacts_count > 0" class="text-xs text-gray-500">
                                    <span x-text="company.contacts_count"></span> 👥
                                </p>
                                <p x-show="company.value > 0" class="text-xs text-green-600 font-medium">
                                    <span x-text="formatCurrency(company.value)"></span>
                                </p>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </template>
        
        <!-- Contacts Results -->
        <template x-if="results.contacts && results.contacts.length > 0">
            <div>
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 border-b">
                    👥 Kontakty (<span x-text="results.contacts.length"></span>)
                </div>
                <template x-for="(contact, index) in results.contacts" :key="contact.id">
                    <a :href="contact.url" 
                       :class="{ 'bg-gray-50 dark:bg-gray-700': selectedIndex === (results.companies.length + index) }"
                       class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 transition-colors duration-150">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300" x-text="getInitials(contact.title)"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-html="contact.highlighted"></p>
                                    <span x-show="contact.is_primary" class="badge badge-blue text-xs">Hlavní</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="contact.subtitle"></p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span x-text="contact.company_icon" class="text-sm"></span>
                                    <p class="text-xs text-gray-400 dark:text-gray-500" x-text="contact.description"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p x-show="contact.email" class="text-xs text-blue-600">📧</p>
                                <p x-show="contact.phone" class="text-xs text-green-600">📞</p>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </template>
        
        <!-- Empty Results -->
        <div x-show="query.length >= 2 && results.total === 0 && !loading" class="px-4 py-8 text-center">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.5-.979-6.074-2.562M12 21C6.477 21 2 16.523 2 12S6.477 3 12 3s10 4.477 10 9c0 3.095-1.409 5.865-3.624 7.504z"></path>
                </svg>
                <p class="mt-2 text-sm">Žádné výsledky pro "<span x-text="query" class="font-medium"></span>"</p>
                <p class="text-xs text-gray-400 mt-1">Zkuste jiné klíčové slovo</p>
            </div>
        </div>
        
        <!-- Search Suggestions -->
        <div x-show="query.length >= 2 && suggestions.length > 0" class="border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                💡 Návrhy
            </div>
            <template x-for="suggestion in suggestions" :key="suggestion.text">
                <button @click="applySuggestion(suggestion.text)" 
                        class="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="suggestion.text"></span>
                    <span class="text-xs text-gray-400 ml-2" x-text="suggestion.type"></span>
                </button>
            </template>
        </div>
    </div>
</div>

<script>
function liveSearch() {
    return {
        query: '',
        results: {
            companies: [],
            contacts: [],
            total: 0
        },
        suggestions: [],
        loading: false,
        showResults: false,
        selectedIndex: -1,
        
        search() {
            if (this.query.length < 2) {
                this.results = { companies: [], contacts: [], total: 0 };
                this.suggestions = [];
                return;
            }
            
            this.loading = true;
            
            // Fetch search results
            fetch(`/api/search/live?q=${encodeURIComponent(this.query)}&limit=8`)
                .then(response => response.json())
                .then(data => {
                    this.results = data;
                    this.selectedIndex = -1;
                })
                .catch(error => console.error('Search error:', error))
                .finally(() => this.loading = false);
                
            // Fetch suggestions
            fetch(`/api/search/suggestions?q=${encodeURIComponent(this.query)}`)
                .then(response => response.json())
                .then(data => {
                    this.suggestions = data;
                })
                .catch(error => console.error('Suggestions error:', error));
        },
        
        clearSearch() {
            this.query = '';
            this.results = { companies: [], contacts: [], total: 0 };
            this.suggestions = [];
            this.showResults = false;
        },
        
        selectNext() {
            const totalItems = this.results.companies.length + this.results.contacts.length;
            if (this.selectedIndex < totalItems - 1) {
                this.selectedIndex++;
            }
        },
        
        selectPrevious() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },
        
        selectCurrent() {
            const totalCompanies = this.results.companies.length;
            if (this.selectedIndex >= 0) {
                let url;
                if (this.selectedIndex < totalCompanies) {
                    url = this.results.companies[this.selectedIndex].url;
                } else {
                    url = this.results.contacts[this.selectedIndex - totalCompanies].url;
                }
                window.location.href = url;
            }
        },
        
        applySuggestion(text) {
            this.query = text;
            this.search();
        },
        
        getInitials(name) {
            return name.split(' ').map(word => word[0]).join('').toUpperCase();
        },
        
        getColorForCategory(category) {
            const colors = {
                'it': '#3b82f6',
                'manufacturing': '#6b7280',
                'services': '#10b981',
                'finance': '#f59e0b',
                'healthcare': '#ef4444',
                'retail': '#8b5cf6',
                'education': '#6366f1',
                'food': '#f97316',
                'transport': '#06b6d4',
                'other': '#6b7280'
            };
            return colors[category] || colors.other;
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'active': 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                'inactive': 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                'prospect': 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200'
            };
            return classes[status] || classes.prospect;
        },
        
        getStatusText(status) {
            const texts = {
                'active': 'Aktivní',
                'inactive': 'Neaktivní',
                'prospect': 'Prospect'
            };
            return texts[status] || status;
        },
        
        formatCurrency(value) {
            return new Intl.NumberFormat('cs-CZ', {
                style: 'currency',
                currency: 'CZK',
                minimumFractionDigits: 0
            }).format(value);
        }
    }
}
</script>
