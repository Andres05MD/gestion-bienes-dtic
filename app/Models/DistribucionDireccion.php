<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUpperCaseAttributes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DistribucionDireccion extends Model
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
            ->setDescriptionForEvent(function(string $eventName) {
                $eventTranslated = match($eventName) {
                    'created' => 'creada',
                    'updated' => 'actualizada',
                    'deleted' => 'eliminada',
                    default   => $eventName,
                };
                return "Distribución N° {$this->numero_bien} fue {$eventTranslated}";
            })
            ->useLogName('distribuciones');
    }

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'distribuciones_direccion';

    /**
     * Los atributos que son asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_bien',
        'descripcion',
        'marca',
        'serial',
        'procedencia_id',
        'fecha',
        'bien_id',
        'bien_externo_id',
        'user_id',
    ];

    /**
     * Los atributos que deben castearse.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /**
     * Relación: departamento de procedencia.
     */
    public function procedencia(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'procedencia_id');
    }

    /**
     * Relación: bien DTIC asociado.
     */
    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    /**
     * Relación: bien externo asociado.
     */
    public function bienExterno(): BelongsTo
    {
        return $this->belongsTo(BienExterno::class);
    }

    /**
     * Relación: usuario que registró la distribución.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
