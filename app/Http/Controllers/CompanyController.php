<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Zobrazit seznam všech firem
     */
    public function index()
    {
        $companies = Company::with('contacts')->paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Zobrazit formulář pro vytvoření nové firmy
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Uložit novou firmu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,prospect',
            'value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Company::create($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Firma byla úspěšně vytvořena.');
    }

    /**
     * Zobrazit detail firmy
     */
    public function show(Company $company)
    {
        $company->load('contacts');
        return view('companies.show', compact('company'));
    }

    /**
     * Zobrazit formulář pro editaci firmy
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Aktualizovat firmu
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,prospect',
            'value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Firma byla úspěšně aktualizována.');
    }

    /**
     * Smazat firmu
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Firma byla úspěšně smazána.');
    }
}
