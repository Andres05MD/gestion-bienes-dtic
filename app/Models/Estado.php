<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function bienes(): HasMany
    {
        return $this->hasMany(Bien::class);
    }

    public function badgeClasses(): string
    {
        return match($this->nombre) {
            'Bueno' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            'Malo' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            'Regular' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            'En Reparacion' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            'Desincorporado' => 'bg-gray-500/10 text-gray-400 border border-gray-500/20',
            default => 'bg-brand-purple/10 text-brand-lila border border-brand-purple/20',
        };
    }
}
