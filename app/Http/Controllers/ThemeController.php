<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    /**
     * Změnit téma uživatele
     */
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:blue,green,purple,orange,red'
        ]);

        $user = Auth::user();
        $success = $user->updateTheme($request->theme);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Téma bylo úspěšně změněno',
                'theme' => $request->theme,
                'theme_info' => $user->getThemeInfo()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Chyba při změně tématu'
        ], 400);
    }

    /**
     * Získat aktuální téma uživatele
     */
    public function current()
    {
        $user = Auth::user();
        
        return response()->json([
            'current_theme' => $user->preferred_theme,
            'theme_info' => $user->getThemeInfo(),
            'available_themes' => $user->getAvailableThemes()
        ]);
    }
}
