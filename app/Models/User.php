<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'preferred_theme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Získat dostupná témata
     */
    public static function getAvailableThemes(): array
    {
        return [
            'blue' => [
                'name' => 'Modrá',
                'primary' => '#3b82f6',
                'description' => 'Klasická modrá barva'
            ],
            'green' => [
                'name' => 'Zelená',
                'primary' => '#10b981',
                'description' => 'Příroda a růst'
            ],
            'purple' => [
                'name' => 'Fialová',
                'primary' => '#8b5cf6',
                'description' => 'Elegance a kreativita'
            ],
            'orange' => [
                'name' => 'Oranžová',
                'primary' => '#f59e0b',
                'description' => 'Energie a teplo'
            ],
            'red' => [
                'name' => 'Červená',
                'primary' => '#ef4444',
                'description' => 'Síla a vášeň'
            ]
        ];
    }

    /**
     * Získat informace o aktuálním tématu
     */
    public function getThemeInfo(): array
    {
        $themes = self::getAvailableThemes();
        return $themes[$this->preferred_theme] ?? $themes['blue'];
    }

    /**
     * Změnit preferované téma
     */
    public function updateTheme(string $theme): bool
    {
        if (array_key_exists($theme, self::getAvailableThemes())) {
            return $this->update(['preferred_theme' => $theme]);
        }
        return false;
    }
}
