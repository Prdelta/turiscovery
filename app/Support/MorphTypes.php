<?php

namespace App\Support;

use App\Models\Candelaria;
use App\Models\CandelariaDanza;
use App\Models\Evento;
use App\Models\Experiencia;
use App\Models\Locale;
use App\Models\Promocion;
use App\Models\User;

/**
 * Alias estables para las relaciones polimórficas (reseñas, favoritos, tokens).
 * En la base se guarda "experiencia" en lugar de "App\Models\Experiencia":
 * así se pueden mover o renombrar clases sin romper datos ni clientes de la API.
 */
final class MorphTypes
{
    public const MAP = [
        'locale' => Locale::class,
        'experiencia' => Experiencia::class,
        'evento' => Evento::class,
        'promocion' => Promocion::class,
        'candelaria' => Candelaria::class,
        'danza' => CandelariaDanza::class,
        'user' => User::class,
    ];

    /** Lo que se puede reseñar */
    public const REVIEWABLE = ['locale', 'experiencia', 'evento', 'promocion', 'candelaria'];

    /** Lo que se puede guardar en favoritos */
    public const FAVORITABLE = ['locale', 'experiencia', 'evento', 'promocion', 'candelaria', 'danza'];

    /**
     * Convierte un tipo recibido (alias o nombre de clase antiguo) a su alias.
     * Devuelve null si no corresponde a ningún modelo conocido.
     */
    public static function normalize(?string $type): ?string
    {
        if ($type === null || $type === '') {
            return null;
        }
        if (array_key_exists($type, self::MAP)) {
            return $type;
        }

        $alias = array_search(ltrim($type, '\\'), self::MAP, true);

        return $alias === false ? null : $alias;
    }

    /** ¿Existe el registro de ese tipo e id? */
    public static function exists(string $alias, int $id): bool
    {
        $class = self::MAP[$alias] ?? null;

        return $class !== null && $class::query()->whereKey($id)->exists();
    }
}
