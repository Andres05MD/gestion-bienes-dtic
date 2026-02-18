<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum que representa los posibles estatus de una operación (transferencia, desincorporación, etc.).
 */
enum EstatusOperacion: string
{
    case ActasListas = 'Actas Listas';
    case ActaFirmadaFaltaCopia = 'Acta Firmada falta Copia';
    case Pendiente = 'Pendiente';

    /**
     * Retorna las clases CSS para badges según el estatus.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::ActasListas => 'bg-green-500/10 text-green-400 border border-green-500/20',
            self::ActaFirmadaFaltaCopia => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Pendiente => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
        };
    }

    /**
     * Retorna la etiqueta legible en español.
     */
    public function label(): string
    {
        return $this->value;
    }

    /**
     * Retorna el ícono asociado al estatus.
     */
    public function icon(): string
    {
        return match ($this) {
            self::ActasListas => 'o-check-circle',
            self::ActaFirmadaFaltaCopia => 'o-document-check',
            self::Pendiente => 'o-clock',
        };
    }

    /**
     * Retorna el color base Tailwind asociado al estatus.
     */
    public function color(): string
    {
        return match ($this) {
            self::ActasListas => 'green-500',
            self::ActaFirmadaFaltaCopia => 'amber-500',
            self::Pendiente => 'rose-500',
        };
    }
}
