@extends('layouts.dashboard')

@section('title', 'Dashboard - CRM System')

@section('dashboard-content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📊 Analytics Dashboard</h1>
    <p class="mt-2 text-gray-600 dark:text-gray-400">Přehled výkonu vašeho CRM systému</p>
    
    <!-- Period Filter -->
    <div class="mt-4 flex space-x-4">
        <select id="periodFilter" class="form-input w-auto" onchange="updatePeriod(this.value)">
            <option value="1month">Poslední měsíc</option>
            <option value="3months">Poslední 3 měsíce</option>
            <option value="6months" selected>Posledních 6 měsíců</option>
            <option value="1year">Poslední rok</option>
        </select>
        <button onclick="exportDashboard()" class="btn-secondary">
            📊 Export dat
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Companies -->
    <div class="card hover:shadow-lg transition-all duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <span class="text-2xl">🏢</span>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Celkem firem</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCompanies) }}</p>
                <span class="text-xs text-green-600">{{ $activeCompanies }} aktivních</span>
            </div>
        </div>
    </div>

    <!-- Total Contacts -->
    <div class="card hover:shadow-lg transition-all duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                <span class="text-2xl">👥</span>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Celkem kontaktů</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalContacts) }}</p>
                <span class="text-xs text-blue-600">{{ number_format($totalContacts / max($totalCompanies, 1), 1) }} na firmu</span>
            </div>
        </div>
    </div>

    <!-- Total Deal Value -->
    <div class="card hover:shadow-lg transition-all duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                <span class="text-2xl">💰</span>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Hodnota obchodů</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalDealsValue, 0, ',', ' ') }} Kč</p>
                <span class="text-xs text-purple-600">∅ {{ number_format($averageDealValue, 0, ',', ' ') }} Kč</span>
            </div>
        </div>
    </div>

    <!-- Conversion Rate -->
    <div class="card hover:shadow-lg transition-all duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                <span class="text-2xl">📈</span>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversion Rate</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analyticsData['conversionRate'] }}%</p>
                <span class="text-xs text-green-600">Prospect → Aktivní</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Companies by Category -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">🏭 Firmy podle kategorií</h3>
        <div class="relative h-80">
            <canvas id="categoriesChart"></canvas>
        </div>
    </div>

    <!-- Companies by Status -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">📊 Firmy podle statusu</h3>
        <div class="relative h-80">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Deal Values by Category -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">💼 Hodnoty obchodů</h3>
        <div class="relative h-80">
            <canvas id="dealValuesChart"></canvas>
        </div>
    </div>

    <!-- Companies Growth -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">📈 Růst počtu firem</h3>
        <div class="relative h-80">
            <canvas id="growthChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Categories & Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Top Categories -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">🏆 Top kategorie</h3>
        <div class="space-y-4">
            @foreach($analyticsData['topCategories'] as $category)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">{{ $category['icon'] }}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category['count'] }} firem</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">
                            {{ number_format(($category['count'] / $totalCompanies) * 100, 1) }}%
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Companies -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">🆕 Nejnovější firmy</h3>
        <div class="space-y-3">
            @foreach($latestCompanies as $company)
                <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <div class="w-8 h-8 {{ $company->getCategoryBgColor() }} rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-sm">{{ $company->getCategoryIcon() }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $company->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $company->created_at->format('d.m.Y') }}</p>
                    </div>
                    <a href="{{ route('companies.show', $company) }}" class="text-blue-600 hover:text-blue-500 text-sm">
                        Zobrazit →
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">👤 Nejnovější kontakty</h3>
        <div class="space-y-3">
            @foreach($latestContacts as $contact)
                <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                            {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $contact->first_name }} {{ $contact->last_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->company->name }}</p>
                    </div>
                    <a href="{{ route('contacts.show', $contact) }}" class="text-blue-600 hover:text-blue-500 text-sm">
                        Zobrazit →
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Analytics data from PHP
const analyticsData = @json($analyticsData);

// Initialize all charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Categories Pie Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: analyticsData.companiesByCategory.map(item => item.label),
            datasets: [{
                data: analyticsData.companiesByCategory.map(item => item.value),
                backgroundColor: [
                    '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444',
                    '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6b7280'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Status Bar Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: analyticsData.companiesByStatus.map(item => item.label),
            datasets: [{
                label: 'Počet firem',
                data: analyticsData.companiesByStatus.map(item => item.value),
                backgroundColor: analyticsData.companiesByStatus.map(item => item.color),
                borderColor: analyticsData.companiesByStatus.map(item => item.color),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Deal Values Chart
    const dealValuesCtx = document.getElementById('dealValuesChart').getContext('2d');
    new Chart(dealValuesCtx, {
        type: 'polarArea',
        data: {
            labels: analyticsData.dealValuesByCategory.map(item => item.label),
            datasets: [{
                data: analyticsData.dealValuesByCategory.map(item => item.value),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.6)', 'rgba(16, 185, 129, 0.6)', 
                    'rgba(139, 92, 246, 0.6)', 'rgba(245, 158, 11, 0.6)', 
                    'rgba(239, 68, 68, 0.6)', 'rgba(6, 182, 212, 0.6)'
                ],
                borderColor: [
                    '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Growth Line Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: analyticsData.companiesGrowth.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('cs-CZ', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Celkem firem',
                data: analyticsData.companiesGrowth.map(item => item.count),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function updatePeriod(period) {
    window.location.href = `?period=${period}`;
}

function exportDashboard() {
    // Simple CSV export - can be enhanced
    const data = analyticsData.companiesByCategory.map(item => 
        `${item.label},${item.value}`
    ).join('\n');
    
    const blob = new Blob([`Kategorie,Počet firem\n${data}`], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'dashboard_export.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
