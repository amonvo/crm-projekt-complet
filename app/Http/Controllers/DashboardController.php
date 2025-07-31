<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Contact;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Zobrazit dashboard s přehledem
     */
    public function index()
    {
        $stats = [
            'companies_count' => Company::count(),
            'contacts_count' => Contact::count(),
            'users_count' => User::count(),
            'active_companies' => Company::where('status', 'active')->count(),
        ];

        $recent_companies = Company::latest()->take(5)->get();
        $recent_contacts = Contact::with('company')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_companies', 'recent_contacts'));
    }
}
