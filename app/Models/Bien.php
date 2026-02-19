<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUpperCaseAttributes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bien extends Model
{
    use HasFactory, LogsActivity, HasUpperCaseAttributes;

    /**
     * Configuración del registro de actividad.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Bien DTIC \"{$this->equipo}\" (N° {$this->numero_bien}) fue {$eventName}")
            ->useLogName('bienes');
    }

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'bienes';

    /**
     * Los atributos que son asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipo',
        'marca',
        'modelo',
        'serial',
        'color',
        'numero_bien',
        'categoria_bien_id',
        'estado_id',
        'observaciones',
        'area_id',
        'user_id',
    ];

    /**
     * Los atributos que deben castearse.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Relación: el usuario que registró el bien.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: la categoría del bien.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaBien::class, 'categoria_bien_id');
    }

    /**
     * Relación: el área de ubicación del bien.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación: el estado del bien.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }
}
