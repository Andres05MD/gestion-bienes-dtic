<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MovimientoBien extends Model
{
    use HasFactory;

    // Constantes para tipos de movimiento
    public const TIPO_REGISTRO = 'registro';
    public const TIPO_TRANSFERENCIA = 'transferencia';
    public const TIPO_DESINCORPORACION = 'desincorporacion';
    public const TIPO_DISTRIBUCION = 'distribucion';
    public const TIPO_MANTENIMIENTO = 'mantenimiento';
    public const TIPO_MANTENIMIENTO_DEVOLUCION = 'mantenimiento_devolucion';

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'movimientos_bienes';

    /**
     * Los atributos que son asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bien_type',
        'bien_id',
        'numero_bien',
        'tipo_movimiento',
        'operacion_type',
        'operacion_id',
        'departamento_origen_id',
        'departamento_destino_id',
        'area_origen_id',
        'area_destino_id',
        'descripcion',
        'fecha',
        'user_id',
    ];

    /**
     * Los atributos que deben castearse.
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /**
     * Etiquetas legibles para cada tipo de movimiento.
     */
    public static function etiquetasTipo(): array
    {
        return [
            self::TIPO_REGISTRO => 'Registro Inicial',
            self::TIPO_TRANSFERENCIA => 'Transferencia',
            self::TIPO_DESINCORPORACION => 'Desincorporación',
            self::TIPO_DISTRIBUCION => 'Distribución',
            self::TIPO_MANTENIMIENTO => 'Envío a Mantenimiento',
            self::TIPO_MANTENIMIENTO_DEVOLUCION => 'Devolución de Mantenimiento',
        ];
    }

    /**
     * Obtener la etiqueta legible del tipo de movimiento.
     */
    public function getEtiquetaTipoAttribute(): string
    {
        return self::etiquetasTipo()[$this->tipo_movimiento] ?? $this->tipo_movimiento;
    }

    /**
     * Relación polimórfica: el bien (Bien o BienExterno).
     */
    public function bien(): MorphTo
    {
        return $this->morphTo('bien');
    }

    /**
     * Relación polimórfica: la operación que generó el movimiento.
     */
    public function operacion(): MorphTo
    {
        return $this->morphTo('operacion');
    }

    /**
     * Relación: departamento de origen.
     */
    public function departamentoOrigen(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_origen_id');
    }

    /**
     * Relación: departamento de destino.
     */
    public function departamentoDestino(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_destino_id');
    }

    /**
     * Relación: área de origen.
     */
    public function areaOrigen(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_origen_id');
    }

    /**
     * Relación: área de destino.
     */
    public function areaDestino(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_destino_id');
    }

    /**
     * Relación: usuario que realizó el movimiento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
