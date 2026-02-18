<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaBien extends Model
{
    use HasFactory;

    protected $table = 'categoria_bienes';
    
    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
