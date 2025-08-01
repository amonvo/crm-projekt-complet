<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Zobrazit dashboard s analytics
     */
    public function index(Request $request)
    {
        // Základní statistiky
        $totalCompanies = Company::count();
        $totalContacts = Contact::count();
        $totalUsers = User::count();
        $activeCompanies = Company::where('status', 'active')->count();
        
        // Celková hodnota obchodů
        $totalDealsValue = Company::whereNotNull('value')->sum('value');
        $averageDealValue = Company::whereNotNull('value')->avg('value') ?? 0;
        
        // Analytics data pro grafy
        $analyticsData = $this->getAnalyticsData($request);
        
        // Nejnovější firmy a kontakty
        $latestCompanies = Company::with('contacts')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $latestContacts = Contact::with('company')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact(
            'totalCompanies',
            'totalContacts', 
            'totalUsers',
            'activeCompanies',
            'totalDealsValue',
            'averageDealValue',
            'analyticsData',
            'latestCompanies',
            'latestContacts'
        ));
    }
    
    /**
     * Získat analytics data pro grafy
     */
    private function getAnalyticsData(Request $request)
    {
        $period = $request->get('period', '6months');
        $startDate = $this->getStartDate($period);
        
        return [
            'companiesByCategory' => $this->getCompaniesByCategory(),
            'companiesByStatus' => $this->getCompaniesByStatus(),
            'dealValuesByCategory' => $this->getDealValuesByCategory(),
            'companiesGrowth' => $this->getCompaniesGrowth($startDate),
            'conversionRate' => $this->getConversionRate(),
            'topCategories' => $this->getTopCategories(),
        ];
    }
    
    /**
     * Firmy podle kategorií
     */
    private function getCompaniesByCategory()
    {
        $categories = Company::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();
            
        $categoryInfo = Company::getAvailableCategories();
        
        return $categories->map(function ($item) use ($categoryInfo) {
            $info = $categoryInfo[$item->category] ?? $categoryInfo['other'];
            return [
                'label' => $info['icon'] . ' ' . $info['name'],
                'value' => $item->count,
                'color' => $info['color'],
                'category' => $item->category
            ];
        });
    }
    
    /**
     * Firmy podle statusu
     */
    private function getCompaniesByStatus()
    {
        return Company::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                $statusLabels = [
                    'active' => '✅ Aktivní',
                    'inactive' => '❌ Neaktivní', 
                    'prospect' => '⏳ Prospect'
                ];
                $colors = [
                    'active' => '#10b981',
                    'inactive' => '#ef4444',
                    'prospect' => '#f59e0b'
                ];
                
                return [
                    'label' => $statusLabels[$item->status] ?? $item->status,
                    'value' => $item->count,
                    'color' => $colors[$item->status] ?? '#6b7280'
                ];
            });
    }
    
    /**
     * Hodnota obchodů podle kategorií
     */
    private function getDealValuesByCategory()
    {
        $values = Company::select('category', DB::raw('SUM(COALESCE(value, 0)) as total_value'))
            ->groupBy('category')
            ->having('total_value', '>', 0)
            ->get();
            
        $categoryInfo = Company::getAvailableCategories();
        
        return $values->map(function ($item) use ($categoryInfo) {
            $info = $categoryInfo[$item->category] ?? $categoryInfo['other'];
            return [
                'label' => $info['icon'] . ' ' . $info['name'],
                'value' => (float) $item->total_value,
                'color' => $info['color'],
                'category' => $item->category
            ];
        });
    }
    
    /**
     * Růst firem v čase
     */
    private function getCompaniesGrowth($startDate)
    {
        $growth = Company::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Vytvoření kontinuálního datasetu
        $period = Carbon::parse($startDate);
        $end = Carbon::now();
        $data = [];
        $runningTotal = Company::where('created_at', '<', $startDate)->count();
        
        while ($period->lte($end)) {
            $dayCount = $growth->where('date', $period->format('Y-m-d'))->first()->count ?? 0;
            $runningTotal += $dayCount;
            
            $data[] = [
                'date' => $period->format('Y-m-d'),
                'count' => $runningTotal,
                'new' => $dayCount
            ];
            
            $period->addDay();
        }
        
        return collect($data)->where('new', '>', 0)->values();
    }
    
    /**
     * Conversion rate
     */
    private function getConversionRate()
    {
        $total = Company::count();
        $prospects = Company::where('status', 'prospect')->count();
        $active = Company::where('status', 'active')->count();
        
        if ($total === 0) return 0;
        
        return round(($active / max($total, 1)) * 100, 2);
    }
    
    /**
     * Top kategorie podle počtu
     */
    private function getTopCategories()
    {
        return Company::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $categoryInfo = Company::getAvailableCategories();
                $info = $categoryInfo[$item->category] ?? $categoryInfo['other'];
                
                return [
                    'category' => $item->category,
                    'name' => $info['name'],
                    'icon' => $info['icon'],
                    'count' => $item->count,
                    'color' => $info['color']
                ];
            });
    }
    
    /**
     * Získat start datum podle periody
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case '1month':
                return Carbon::now()->subMonth();
            case '3months':
                return Carbon::now()->subMonths(3);
            case '6months':
                return Carbon::now()->subMonths(6);
            case '1year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subMonths(6);
        }
    }
}
