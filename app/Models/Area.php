<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasUpperCaseAttributes;

class Area extends Model
{
    use HasFactory, HasUpperCaseAttributes;

    protected $fillable = ['nombre', 'descripcion'];

    public function bienes(): HasMany
    {
        return $this->hasMany(Bien::class);
    }
}
