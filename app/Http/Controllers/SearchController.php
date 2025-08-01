<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Live search pro firmy a kontakty
     */
    public function liveSearch(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // all, companies, contacts
        $limit = $request->get('limit', 10);
        
        if (strlen($query) < 2) {
            return response()->json([
                'companies' => [],
                'contacts' => [],
                'total' => 0
            ]);
        }
        
        $results = [];
        
        // Search companies
        if (in_array($type, ['all', 'companies'])) {
            $companies = Company::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('industry', 'LIKE', "%{$query}%")
                  ->orWhere('website', 'LIKE', "%{$query}%");
            })
            ->with('contacts')
            ->limit($limit)
            ->get()
            ->map(function ($company) use ($query) {
                return [
                    'id' => $company->id,
                    'type' => 'company',
                    'title' => $company->name,
                    'subtitle' => $company->industry ?: $company->getCategoryName(),
                    'description' => $company->email ?: $company->website,
                    'icon' => $company->getCategoryIcon(),
                    'category' => $company->category,
                    'status' => $company->status,
                    'url' => route('companies.show', $company),
                    'highlighted' => $this->highlightMatch($company->name, $query),
                    'contacts_count' => $company->contacts->count(),
                    'value' => $company->value
                ];
            });
            
            $results['companies'] = $companies;
        }
        
        // Search contacts
        if (in_array($type, ['all', 'contacts'])) {
            $contacts = Contact::where(function($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('position', 'LIKE', "%{$query}%");
            })
            ->with('company')
            ->limit($limit)
            ->get()
            ->map(function ($contact) use ($query) {
                $fullName = $contact->first_name . ' ' . $contact->last_name;
                return [
                    'id' => $contact->id,
                    'type' => 'contact',
                    'title' => $fullName,
                    'subtitle' => $contact->position ?: 'Kontakt',
                    'description' => $contact->company->name,
                    'icon' => '👤',
                    'company_icon' => $contact->company->getCategoryIcon(),
                    'is_primary' => $contact->is_primary,
                    'url' => route('contacts.show', $contact),
                    'highlighted' => $this->highlightMatch($fullName, $query),
                    'email' => $contact->email,
                    'phone' => $contact->phone
                ];
            });
            
            $results['contacts'] = $contacts;
        }
        
        $total = ($results['companies'] ?? collect())->count() + 
                ($results['contacts'] ?? collect())->count();
        
        return response()->json([
            'companies' => $results['companies'] ?? [],
            'contacts' => $results['contacts'] ?? [],
            'total' => $total,
            'query' => $query
        ]);
    }
    
    /**
     * Pokročilé filtrování firem
     */
    public function filterCompanies(Request $request)
    {
        $query = Company::with('contacts');
        
        // Filtr podle kategorie
        if ($request->filled('category')) {
            $categories = is_array($request->category) ? $request->category : [$request->category];
            $query->whereIn('category', $categories);
        }
        
        // Filtr podle statusu
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }
        
        // Filtr podle hodnoty obchodu
        if ($request->filled('value_min')) {
            $query->where('value', '>=', $request->value_min);
        }
        if ($request->filled('value_max')) {
            $query->where('value', '<=', $request->value_max);
        }
        
        // Filtr podle data vytvoření
        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', $request->created_to . ' 23:59:59');
        }
        
        // Filtr podle počtu kontaktů
        if ($request->filled('contacts_min')) {
            $query->has('contacts', '>=', $request->contacts_min);
        }
        
        // Textové vyhledávání
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('industry', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Řazení
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Paginace nebo všechny výsledky
        if ($request->get('all') === 'true') {
            $companies = $query->get();
            return response()->json([
                'data' => $companies,
                'total' => $companies->count(),
                'filtered' => true
            ]);
        }
        
        $perPage = $request->get('per_page', 15);
        $companies = $query->paginate($perPage);
        
        return response()->json([
            'data' => $companies->items(),
            'current_page' => $companies->currentPage(),
            'last_page' => $companies->lastPage(),
            'per_page' => $companies->perPage(),
            'total' => $companies->total(),
            'filtered' => true
        ]);
    }
    
    /**
     * Autocomplete suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $suggestions = [];
        
        // Company name suggestions
        if (in_array($type, ['all', 'companies'])) {
            $companyNames = Company::where('name', 'LIKE', "%{$query}%")
                ->limit(5)
                ->pluck('name')
                ->map(function($name) {
                    return ['text' => $name, 'type' => 'company_name'];
                });
            $suggestions = array_merge($suggestions, $companyNames->toArray());
        }
        
        // Industry suggestions
        if (in_array($type, ['all', 'companies'])) {
            $industries = Company::where('industry', 'LIKE', "%{$query}%")
                ->whereNotNull('industry')
                ->distinct()
                ->limit(3)
                ->pluck('industry')
                ->map(function($industry) {
                    return ['text' => $industry, 'type' => 'industry'];
                });
            $suggestions = array_merge($suggestions, $industries->toArray());
        }
        
        // Contact name suggestions
        if (in_array($type, ['all', 'contacts'])) {
            $contactNames = Contact::where(function($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function($contact) {
                return [
                    'text' => $contact->first_name . ' ' . $contact->last_name,
                    'type' => 'contact_name'
                ];
            });
            $suggestions = array_merge($suggestions, $contactNames->toArray());
        }
        
        return response()->json(array_slice($suggestions, 0, 10));
    }
    
    /**
     * Zvýraznění nalezených výsledků
     */
    private function highlightMatch($text, $query)
    {
        return preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark class="bg-yellow-200 dark:bg-yellow-600">$1</mark>',
            $text
        );
    }
    
    /**
     * Bulk operations
     */
    public function bulkOperation(Request $request)
    {
        $operation = $request->get('operation');
        $ids = $request->get('ids', []);
        $type = $request->get('type', 'companies'); // companies, contacts
        
        if (empty($ids)) {
            return response()->json(['error' => 'Nejsou vybrány žádné položky'], 400);
        }
        
        $results = [];
        
        switch ($operation) {
            case 'delete':
                if ($type === 'companies') {
                    $deleted = Company::whereIn('id', $ids)->delete();
                    $results = ['deleted' => $deleted, 'message' => "Smazáno {$deleted} firem"];
                } else {
                    $deleted = Contact::whereIn('id', $ids)->delete();
                    $results = ['deleted' => $deleted, 'message' => "Smazáno {$deleted} kontaktů"];
                }
                break;
                
            case 'change_status':
                $newStatus = $request->get('new_status');
                if ($type === 'companies' && in_array($newStatus, ['active', 'inactive', 'prospect'])) {
                    $updated = Company::whereIn('id', $ids)->update(['status' => $newStatus]);
                    $results = ['updated' => $updated, 'message' => "Změněn status u {$updated} firem"];
                }
                break;
                
            case 'export_selected':
                if ($type === 'companies') {
                    $companies = Company::whereIn('id', $ids)->get();
                    $csvData = $this->exportCompaniesToCsv($companies);
                    return response($csvData)
                        ->header('Content-Type', 'text/csv')
                        ->header('Content-Disposition', 'attachment; filename="selected_companies.csv"');
                }
                break;
        }
        
        return response()->json($results);
    }
    
    /**
     * Export firem do CSV
     */
    private function exportCompaniesToCsv($companies)
    {
        $csv = "Název,Email,Telefon,Kategorie,Status,Hodnota,Vytvořeno\n";
        foreach ($companies as $company) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $company->name,
                $company->email ?: '',
                $company->phone ?: '',
                $company->getCategoryName(),
                $company->status,
                $company->value ?: '0',
                $company->created_at->format('d.m.Y')
            );
        }
        return $csv;
    }
}
