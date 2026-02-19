<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUpperCaseAttributes
{
    /**
     * El "booting" del trait.
     * Laravel llama automáticamente a este método al iniciar el modelo.
     */
    protected static function bootHasUpperCaseAttributes()
    {
        static::saving(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                // Solo procesar si es una cadena y no está en la lista de excluidos
                if (is_string($value) && !self::shouldExclude($key)) {
                    $model->{$key} = Str::upper($value);
                }
            }
        });
    }

    /**
     * Determina si un atributo debe ser excluido de la conversión a mayúsculas.
     *
     * @param string $key
     * @return bool
     */
    protected static function shouldExclude($key)
    {
        $exclude = [
            'password',
            'email',
            'remember_token',
            'created_at',
            'updated_at',
            'deleted_at',
            'email_verified_at',
            'two_factor_recovery_codes',
            'two_factor_secret',
        ];

        // No convertir claves primarias, foráneas o campos que terminen en _id, _url, _path, _color, _icon
        return in_array($key, $exclude) || 
               Str::endsWith($key, '_id') || 
               Str::endsWith($key, '_url') || 
               Str::endsWith($key, '_path') || 
               Str::endsWith($key, '_color') || 
               Str::endsWith($key, '_icon') ||
               $key === 'id';
    }
}
