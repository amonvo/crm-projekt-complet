@extends('layouts.dashboard')

@section('title', 'Firmy - CRM System')

@section('dashboard-content')
<div x-data="companiesManager()" class="space-y-6">
    <!-- Header s akcemi -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🏢 Správa firem</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Celkem <span x-text="totalCompanies"></span> firem
                <span x-show="selectedCompanies.length > 0" class="ml-2 font-medium text-blue-600">
                    · <span x-text="selectedCompanies.length"></span> vybráno
                </span>
            </p>
        </div>
        <div class="flex space-x-3">
            <!-- Bulk Actions -->
            <div x-show="selectedCompanies.length > 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-1 scale-100"
                 class="flex space-x-2">
                <button @click="bulkChangeStatus()" class="btn-secondary text-sm">
                    📝 Změnit status
                </button>
                <button @click="bulkExport()" class="btn-secondary text-sm">
                    📊 Export CSV
                </button>
                <button @click="bulkDelete()" class="btn-danger text-sm">
                    🗑️ Smazat
                </button>
            </div>
            
            <a href="{{ route('companies.create') }}" class="btn-primary">
                ➕ Přidat firmu
            </a>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">🔍 Pokročilé filtry</h3>
            <button @click="showFilters = !showFilters" 
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <span x-show="!showFilters">Zobrazit filtry</span>
                <span x-show="showFilters">Skrýt filtry</span>
            </button>
        </div>
        
        <div x-show="showFilters" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 height-0"
             x-transition:enter-end="opacity-1 height-auto"
             class="space-y-4">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="form-label">Vyhledat</label>
                    <input type="text" 
                           x-model="filters.search" 
                           @input.debounce.300ms="applyFilters()"
                           class="form-input" 
                           placeholder="Název, email, odvětví...">
                </div>
                
                <!-- Category Filter -->
                <div>
                    <label class="form-label">Kategorie</label>
                    <select x-model="filters.category" @change="applyFilters()" class="form-input">
                        <option value="">Všechny kategorie</option>
                        <option value="it">💻 IT & Technologie</option>
                        <option value="manufacturing">🏭 Výroba</option>
                        <option value="services">🛠️ Služby</option>
                        <option value="finance">💰 Finance</option>
                        <option value="healthcare">🏥 Zdravotnictví</option>
                        <option value="retail">🛒 Maloobchod</option>
                        <option value="education">📚 Vzdělávání</option>
                        <option value="food">🍽️ Pohostinství</option>
                        <option value="transport">🚚 Doprava</option>
                        <option value="other">🏢 Ostatní</option>
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label class="form-label">Status</label>
                    <select x-model="filters.status" @change="applyFilters()" class="form-input">
                        <option value="">Všechny statusy</option>
                        <option value="active">✅ Aktivní</option>
                        <option value="inactive">❌ Neaktivní</option>
                        <option value="prospect">⏳ Prospect</option>
                    </select>
                </div>
                
                <!-- Value Range -->
                <div>
                    <label class="form-label">Hodnota obchodu</label>
                    <div class="flex space-x-2">
                        <input type="number" 
                               x-model="filters.value_min" 
                               @input.debounce.500ms="applyFilters()"
                               class="form-input w-1/2" 
                               placeholder="Od">
                        <input type="number" 
                               x-model="filters.value_max" 
                               @input.debounce.500ms="applyFilters()"
                               class="form-input w-1/2" 
                               placeholder="Do">
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="form-label">Vytvořeno od</label>
                    <input type="date" 
                           x-model="filters.created_from" 
                           @change="applyFilters()"
                           class="form-input">
                </div>
                
                <div>
                    <label class="form-label">Vytvořeno do</label>
                    <input type="date" 
                           x-model="filters.created_to" 
                           @change="applyFilters()"
                           class="form-input">
                </div>
                
                <!-- Sort Options -->
                <div>
                    <label class="form-label">Řazení</label>
                    <select x-model="filters.sort_by" @change="applyFilters()" class="form-input">
                        <option value="name">Podle názvu</option>
                        <option value="created_at">Podle data vytvoření</option>
                        <option value="value">Podle hodnoty</option>
                        <option value="status">Podle statusu</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <button @click="clearFilters()" class="btn-secondary text-sm">
                        🔄 Vymazat filtry
                    </button>
                    <button @click="saveCurrentFilter()" class="btn-secondary text-sm">
                        💾 Uložit filtr
                    </button>
                </div>
                
                <div class="text-sm text-gray-500">
                    <span x-text="filteredCount"></span> výsledků z <span x-text="totalCompanies"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Companies Table -->
    <div class="card p-0 overflow-hidden">
        <!-- Table Header with Bulk Select -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                           @change="toggleSelectAll()"
                           :checked="selectedCompanies.length === companies.length && companies.length > 0"
                           :indeterminate="selectedCompanies.length > 0 && selectedCompanies.length < companies.length"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <span x-show="selectedCompanies.length === 0">Vybrat vše</span>
                        <span x-show="selectedCompanies.length > 0">
                            <span x-text="selectedCompanies.length"></span> vybráno
                        </span>
                    </span>
                </div>
                
                <div x-show="loading" class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="loading-spinner"></div>
                    <span>Načítání...</span>
                </div>
            </div>
        </div>

        <!-- Loading Skeleton -->
        <div x-show="loading" class="p-6">
            <template x-for="i in 5" :key="i">
                <div class="animate-pulse mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-4 h-4 bg-gray-300 rounded"></div>
                        <div class="w-12 h-12 bg-gray-300 rounded-lg"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-300 rounded w-1/2"></div>
                        </div>
                        <div class="w-20 h-6 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Companies List -->
        <div x-show="!loading" class="divide-y divide-gray-200 dark:divide-gray-700">
            <template x-for="company in companies" :key="company.id">
                <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center space-x-4">
                        <!-- Checkbox -->
                        <input type="checkbox" 
                               :value="company.id"
                               x-model="selectedCompanies"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        
                        <!-- Company Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center text-xl"
                             :style="{ backgroundColor: getCategoryColor(company.category) + '20' }">
                            <span x-text="getCategoryIcon(company.category)"></span>
                        </div>
                        
                        <!-- Company Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="company.name"></h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                      :class="getStatusBadgeClass(company.status)">
                                    <span x-text="getStatusText(company.status)"></span>
                                </span>
                            </div>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                      :style="{ backgroundColor: getCategoryColor(company.category) + '20', color: getCategoryColor(company.category) }">
                                    <span x-text="getCategoryIcon(company.category)"></span>
                                    <span class="ml-1" x-text="getCategoryName(company.category)"></span>
                                </span>
                                <span x-show="company.industry" class="text-xs text-gray-500" x-text="company.industry"></span>
                            </div>
                            <div class="flex items-center space-x-4 mt-2">
                                <span x-show="company.email" class="text-xs text-gray-500">
                                    📧 <span x-text="company.email"></span>
                                </span>
                                <span x-show="company.phone" class="text-xs text-gray-500">
                                    📞 <span x-text="company.phone"></span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Company Stats -->
                        <div class="text-right">
                            <div x-show="company.value > 0" class="text-sm font-medium text-green-600">
                                <span x-text="formatCurrency(company.value)"></span>
                            </div>
                            <div class="flex items-center space-x-1 text-xs text-gray-500">
                                <span x-text="company.contacts_count || 0"></span>
                                <span>👥</span>
                            </div>
                            <div class="text-xs text-gray-400" x-text="formatDate(company.created_at)"></div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <a :href="`/companies/${company.id}`" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                👁️ Detail
                            </a>
                            <a :href="`/companies/${company.id}/edit`" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                ✏️ Upravit
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && companies.length === 0" class="p-12 text-center">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="mt-2 text-sm font-medium">Žádné firmy nenalezeny</p>
                <p class="text-xs text-gray-400 mt-1">Zkuste upravit filtry nebo přidat novou firmu</p>
            </div>
        </div>

        <!-- Load More Button -->
        <div x-show="!loading && hasMore" class="p-4 text-center border-t border-gray-200 dark:border-gray-700">
            <button @click="loadMore()" class="btn-secondary">
                Načíst další firmy
            </button>
        </div>
    </div>
</div>

<!-- Bulk Status Change Modal -->
<div x-show="showStatusModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-1"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Změnit status firem</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Měnit status u <span x-text="selectedCompanies.length"></span> vybraných firem na:
        </p>
        <select x-model="newStatus" class="form-input mb-4 w-full">
            <option value="active">✅ Aktivní</option>
            <option value="inactive">❌ Neaktivní</option>
            <option value="prospect">⏳ Prospect</option>
        </select>
        <div class="flex justify-end space-x-3">
            <button @click="showStatusModal = false" class="btn-secondary">Zrušit</button>
            <button @click="confirmBulkStatusChange()" class="btn-primary">Změnit status</button>
        </div>
    </div>
</div>

<script>
function companiesManager() {
    return {
        companies: @json($companies->items()),
        totalCompanies: {{ $companies->total() }},
        filteredCount: {{ $companies->total() }},
        currentPage: {{ $companies->currentPage() }},
        lastPage: {{ $companies->lastPage() }},
        hasMore: {{ $companies->hasMorePages() ? 'true' : 'false' }},
        
        selectedCompanies: [],
        loading: false,
        showFilters: false,
        showStatusModal: false,
        newStatus: 'active',
        
        filters: {
            search: '',
            category: '',
            status: '',
            value_min: '',
            value_max: '',
            created_from: '',
            created_to: '',
            sort_by: 'name'
        },
        
        // Category data
        categories: {
            'it': { name: 'IT & Technologie', icon: '💻', color: '#3b82f6' },
            'manufacturing': { name: 'Výroba', icon: '🏭', color: '#6b7280' },
            'services': { name: 'Služby', icon: '🛠️', color: '#10b981' },
            'finance': { name: 'Finance', icon: '💰', color: '#f59e0b' },
            'healthcare': { name: 'Zdravotnictví', icon: '🏥', color: '#ef4444' },
            'retail': { name: 'Maloobchod', icon: '🛒', color: '#8b5cf6' },
            'education': { name: 'Vzdělávání', icon: '📚', color: '#6366f1' },
            'food': { name: 'Pohostinství', icon: '🍽️', color: '#f97316' },
            'transport': { name: 'Doprava', icon: '🚚', color: '#06b6d4' },
            'other': { name: 'Ostatní', icon: '🏢', color: '#6b7280' }
        },
        
        applyFilters() {
            this.loading = true;
            this.currentPage = 1;
            
            const params = new URLSearchParams();
            Object.entries(this.filters).forEach(([key, value]) => {
                if (value) params.append(key, value);
            });
            
            fetch(`/api/companies/filter?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    this.companies = data.data;
                    this.filteredCount = data.total;
                    this.currentPage = data.current_page || 1;
                    this.lastPage = data.last_page || 1;
                    this.hasMore = this.currentPage < this.lastPage;
                    this.selectedCompanies = [];
                })
                .catch(error => console.error('Filter error:', error))
                .finally(() => this.loading = false);
        },
        
        loadMore() {
            if (this.loading || !this.hasMore) return;
            
            this.loading = true;
            const params = new URLSearchParams();
            Object.entries(this.filters).forEach(([key, value]) => {
                if (value) params.append(key, value);
            });
            params.append('page', this.currentPage + 1);
            
            fetch(`/api/companies/filter?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    this.companies = [...this.companies, ...data.data];
                    this.currentPage = data.current_page;
                    this.hasMore = this.currentPage < this.lastPage;
                })
                .catch(error => console.error('Load more error:', error))
                .finally(() => this.loading = false);
        },
        
        clearFilters() {
            this.filters = {
                search: '',
                category: '',
                status: '',
                value_min: '',
                value_max: '',
                created_from: '',
                created_to: '',
                sort_by: 'name'
            };
            this.applyFilters();
        },
        
        toggleSelectAll() {
            if (this.selectedCompanies.length === this.companies.length) {
                this.selectedCompanies = [];
            } else {
                this.selectedCompanies = this.companies.map(c => c.id);
            }
        },
        
        bulkChangeStatus() {
            if (this.selectedCompanies.length === 0) return;
            this.showStatusModal = true;
        },
        
        confirmBulkStatusChange() {
            this.performBulkOperation('change_status', { new_status: this.newStatus });
            this.showStatusModal = false;
        },
        
        bulkExport() {
            this.performBulkOperation('export_selected');
        },
        
        bulkDelete() {
            if (!confirm(`Opravdu chcete smazat ${this.selectedCompanies.length} vybraných firem?`)) return;
            this.performBulkOperation('delete');
        },
        
        performBulkOperation(operation, extraData = {}) {
            const data = {
                operation: operation,
                type: 'companies',
                ids: this.selectedCompanies,
                ...extraData
            };
            
            fetch('/api/bulk-operation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (operation === 'export_selected') {
                    return response.blob().then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'selected_companies.csv';
                        a.click();
                        window.URL.revokeObjectURL(url);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data && data.message) {
                    alert(data.message);
                    this.applyFilters(); // Refresh list
                    this.selectedCompanies = [];
                }
            })
            .catch(error => {
                console.error('Bulk operation error:', error);
                alert('Chyba při provádění operace');
            });
        },
        
        // Helper methods
        getCategoryIcon(category) {
            return this.categories[category]?.icon || '🏢';
        },
        
        getCategoryName(category) {
            return this.categories[category]?.name || 'Ostatní';
        },
        
        getCategoryColor(category) {
            return this.categories[category]?.color || '#6b7280';
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'active': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                'inactive': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                'prospect': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            };
            return classes[status] || classes.prospect;
        },
        
        getStatusText(status) {
            const texts = {
                'active': '✅ Aktivní',
                'inactive': '❌ Neaktivní',
                'prospect': '⏳ Prospect'
            };
            return texts[status] || status;
        },
        
        formatCurrency(value) {
            return new Intl.NumberFormat('cs-CZ', {
                style: 'currency',
                currency: 'CZK',
                minimumFractionDigits: 0
            }).format(value);
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('cs-CZ', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
    }
}
</script>
@endsection
