<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUpperCaseAttributes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Desincorporacion extends Model
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
            ->setDescriptionForEvent(function (string $eventName) {
                $eventTranslated = match ($eventName) {
                    'created' => 'creada',
                    'updated' => 'actualizada',
                    'deleted' => 'eliminada',
                    default   => $eventName,
                };
                return "Desincorporación N° {$this->numero_bien} fue {$eventTranslated}";
            })
            ->useLogName('desincorporaciones');
    }

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'desincorporaciones';

    /**
     * Los atributos que son asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo_acta',
        'numero_bien',
        'descripcion',
        'serial',
        'procedencia_id',
        'destino_id',
        'fecha',
        'numero_informe',
        'estatus_acta_id',
        'estatus_acta_individual_id',
        'observaciones',
        'bien_id',
        'bien_externo_id',
        'area_id',
        'area_procedencia_id',
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
     * Relación: estatus del acta (grupal).
     */
    public function estatusActa(): BelongsTo
    {
        return $this->belongsTo(EstatusActa::class);
    }

    /**
     * Relación: estatus del acta individual (override por ítem).
     */
    public function estatusActaIndividual(): BelongsTo
    {
        return $this->belongsTo(EstatusActa::class, 'estatus_acta_individual_id');
    }

    /**
     * Accessor: devuelve el estatus efectivo (individual si existe, grupal como fallback).
     */
    public function getEstatusEfectivoAttribute(): ?EstatusActa
    {
        return $this->estatusActaIndividual ?? $this->estatusActa;
    }

    /**
     * Relación: departamento de procedencia.
     */
    public function procedencia(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'procedencia_id');
    }

    /**
     * Relación: departamento de destino.
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'destino_id');
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
     * Relación: área de destino en DTIC.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Relación: área de procedencia en DTIC.
     */
    public function areaProcedencia(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_procedencia_id');
    }

    /**
     * Relación: usuario que registró la desincorporación.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
