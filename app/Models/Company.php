<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'website',
        'industry',
        'category',
        'status',
        'value',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Vztah s kontakty
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Dostupné kategorie firem s ikonami
     */
    public static function getAvailableCategories(): array
    {
        return [
            'it' => [
                'name' => 'IT & Technologie',
                'icon' => '💻',
                'color' => 'text-blue-600',
                'bg_color' => 'bg-blue-100',
                'description' => 'Software, hardware, IT služby'
            ],
            'manufacturing' => [
                'name' => 'Výroba',
                'icon' => '🏭',
                'color' => 'text-gray-600',
                'bg_color' => 'bg-gray-100',
                'description' => 'Průmyslová výroba, strojírenství'
            ],
            'services' => [
                'name' => 'Služby',
                'icon' => '🛠️',
                'color' => 'text-green-600',
                'bg_color' => 'bg-green-100',
                'description' => 'Poradenství, služby, consulting'
            ],
            'finance' => [
                'name' => 'Finance',
                'icon' => '💰',
                'color' => 'text-yellow-600',
                'bg_color' => 'bg-yellow-100',
                'description' => 'Bankovnictví, pojišťovnictví, finance'
            ],
            'healthcare' => [
                'name' => 'Zdravotnictví',
                'icon' => '🏥',
                'color' => 'text-red-600',
                'bg_color' => 'bg-red-100',
                'description' => 'Nemocnice, kliniky, zdravotní služby'
            ],
            'retail' => [
                'name' => 'Maloobchod',
                'icon' => '🛒',
                'color' => 'text-purple-600',
                'bg_color' => 'bg-purple-100',
                'description' => 'Obchody, e-commerce, retail'
            ],
            'education' => [
                'name' => 'Vzdělávání',
                'icon' => '📚',
                'color' => 'text-indigo-600',
                'bg_color' => 'bg-indigo-100',
                'description' => 'Školy, univerzity, kurzy'
            ],
            'food' => [
                'name' => 'Pohostinství',
                'icon' => '🍽️',
                'color' => 'text-orange-600',
                'bg_color' => 'bg-orange-100',
                'description' => 'Restaurace, hotely, catering'
            ],
            'transport' => [
                'name' => 'Doprava',
                'icon' => '🚚',
                'color' => 'text-blue-500',
                'bg_color' => 'bg-blue-50',
                'description' => 'Logistika, doprava, přeprava'
            ],
            'other' => [
                'name' => 'Ostatní',
                'icon' => '🏢',
                'color' => 'text-gray-500',
                'bg_color' => 'bg-gray-50',
                'description' => 'Jiné odvětví'
            ]
        ];
    }

    /**
     * Získat informace o kategorii firmy
     */
    public function getCategoryInfo(): array
    {
        $categories = self::getAvailableCategories();
        return $categories[$this->category] ?? $categories['other'];
    }

    /**
     * Získat ikonu kategorie
     */
    public function getCategoryIcon(): string
    {
        $categoryInfo = $this->getCategoryInfo();
        return $categoryInfo['icon'];
    }

    /**
     * Získat název kategorie
     */
    public function getCategoryName(): string
    {
        $categoryInfo = $this->getCategoryInfo();
        return $categoryInfo['name'];
    }

    /**
     * Získat barvu kategorie
     */
    public function getCategoryColor(): string
    {
        $categoryInfo = $this->getCategoryInfo();
        return $categoryInfo['color'];
    }

    /**
     * Získat background barvu kategorie
     */
    public function getCategoryBgColor(): string
    {
        $categoryInfo = $this->getCategoryInfo();
        return $categoryInfo['bg_color'];
    }

    /**
     * Scope pro filtrování podle kategorie
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope pro aktivní firmy
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
