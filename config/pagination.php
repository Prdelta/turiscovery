<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Valores por defecto de paginación
    |--------------------------------------------------------------------------
    |
    | Define los valores de per_page por defecto para cada tipo de recurso.
    | Estos valores son usados por ValidatesPaginationTrait.
    |
    */

    'defaults' => [
        'locales' => env('PAGINATION_LOCALES', 15),
        'eventos' => env('PAGINATION_EVENTOS', 15),
        'experiencias' => env('PAGINATION_EXPERIENCIAS', 12),
        'promociones' => env('PAGINATION_PROMOCIONES', 12),
        'candelaria' => env('PAGINATION_CANDELARIA', 15),
        'reviews' => env('PAGINATION_REVIEWS', 10),
        'bookings' => env('PAGINATION_BOOKINGS', 20),
        'favorites' => env('PAGINATION_FAVORITES', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Límites de paginación
    |--------------------------------------------------------------------------
    |
    | Establece los límites mínimo y máximo de items por página.
    | El máximo previene ataques DOS mediante solicitudes excesivas.
    |
    */

    'max_per_page' => env('PAGINATION_MAX_PER_PAGE', 100),
    'min_per_page' => 1,

    /*
    |--------------------------------------------------------------------------
    | Valor por defecto genérico
    |--------------------------------------------------------------------------
    |
    | Usado cuando no se especifica un tipo de recurso específico.
    |
    */

    'default' => env('PAGINATION_DEFAULT', 15),
];
