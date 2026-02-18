<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum que representa los posibles estados de un bien.
 */
enum EstadoBien: string
{
    case Bueno = 'Bueno';
    case Regular = 'Regular';
    case Malo = 'Malo';
    case EnReparacion = 'En Reparación';
    case Desincorporado = 'Desincorporado';

    /**
     * Retorna el color CSS para badges según el estado.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Bueno => 'bg-green-500/10 text-green-400 border border-green-500/20',
            self::Regular => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Malo => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            self::EnReparacion => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            self::Desincorporado => 'bg-gray-500/10 text-gray-400 border border-gray-500/20',
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
     * Retorna el ícono asociado al estado.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Bueno => 'o-check-circle',
            self::Regular => 'o-exclamation-circle',
            self::Malo => 'o-x-circle',
            self::EnReparacion => 'o-wrench-screwdriver',
            self::Desincorporado => 'o-trash',
        };
    }

    /**
     * Retorna el color base Tailwind asociado al estado.
     */
    public function color(): string
    {
        return match ($this) {
            self::Bueno => 'green-500',
            self::Regular => 'amber-500',
            self::Malo => 'rose-500',
            self::EnReparacion => 'blue-500',
            self::Desincorporado => 'gray-500',
        };
    }
}
