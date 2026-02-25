<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasUpperCaseAttributes;

class Estado extends Model
{
    use HasFactory, HasUpperCaseAttributes;

    protected $fillable = ['nombre', 'descripcion', 'color'];

    public function bienes(): HasMany
    {
        return $this->hasMany(Bien::class);
    }

    public function resolveColor(): string
    {
        return $this->color ?? match ($this->nombre) {
            'BUENO' => '#34d399',
            'MALO' => '#fb7185',
            'REGULAR' => '#fbbf24',
            'EN REPARACION' => '#60a5fa',
            'DESINCORPORADO' => '#9ca3af',
            default => '#d8b4fe',
        };
    }

    public function badgeStyles(): string
    {
        $hex = $this->resolveColor();

        return "background-color: {$hex}20; color: {$hex}; border: 1px solid {$hex}50;";
    }
}
