<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Zobrazit seznam všech kontaktů
     */
    public function index()
    {
        $contacts = Contact::with('company')->paginate(10);
        return view('contacts.index', compact('contacts'));
    }

    /**
     * Zobrazit formulář pro vytvoření nového kontaktu
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        return view('contacts.create', compact('companies'));
    }

    /**
     * Uložit nový kontakt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        // Nastavíme default hodnotu pro is_primary pokud nebyl poslán
        $validated['is_primary'] = $request->has('is_primary') ? (bool)$request->input('is_primary') : false;

        // Zajistíme, že pouze jeden kontakt může být primární pro danou firmu
        if ($validated['is_primary']) {
            Contact::where('company_id', $validated['company_id'])
                   ->update(['is_primary' => false]);
        }

        Contact::create($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Kontakt byl úspěšně vytvořen.');
    }

    /**
     * Zobrazit detail kontaktu
     */
    public function show(Contact $contact)
    {
        $contact->load('company');
        return view('contacts.show', compact('contact'));
    }

    /**
     * Zobrazit formulář pro editaci kontaktu
     */
    public function edit(Contact $contact)
    {
        $companies = Company::orderBy('name')->get();
        return view('contacts.edit', compact('contact', 'companies'));
    }

    /**
     * Aktualizovat kontakt
     */
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        // Nastavíme default hodnotu pro is_primary pokud nebyl poslán
        $validated['is_primary'] = $request->has('is_primary') ? (bool)$request->input('is_primary') : false;

        // Zajistíme, že pouze jeden kontakt může být primární pro danou firmu
        if ($validated['is_primary']) {
            Contact::where('company_id', $validated['company_id'])
                   ->where('id', '!=', $contact->id)
                   ->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Kontakt byl úspěšně aktualizován.');
    }

    /**
     * Smazat kontakt
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Kontakt byl úspěšně smazán.');
    }
}
