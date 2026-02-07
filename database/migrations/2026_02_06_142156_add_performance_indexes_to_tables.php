<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega índices a las tablas principales para mejorar el rendimiento de queries.
     * Los índices aceleran significativamente las consultas SELECT, WHERE, ORDER BY y JOIN.
     */
    public function up(): void
    {
        // ========== Tabla: locales ==========
        if (Schema::hasTable('locales')) {
            Schema::table('locales', function (Blueprint $table) {
                // Índices para filtros comunes
                if (!$this->hasIndex('locales', 'locales_is_active_index')) {
                    $table->index('is_active'); // Filtro active()
                }
                if (!$this->hasIndex('locales', 'locales_category_index')) {
                    $table->index('category'); // Filtro byCategory()
                }
                if (!$this->hasIndex('locales', 'locales_user_id_index')) {
                    $table->index('user_id'); // JOIN con users
                }

                // Índices compuestos para queries complejas
                if (!$this->hasIndex('locales', 'locales_active_category_index')) {
                    $table->index(['is_active', 'category', 'created_at']); // Listados filtrados
                }

                // Índice para búsqueda de texto
                if (!$this->hasIndex('locales', 'locales_search_index')) {
                    $table->index(['name', 'address']); // Búsqueda por nombre/dirección
                }
            });
        }

        // ========== Tabla: eventos ==========
        if (Schema::hasTable('eventos')) {
            Schema::table('eventos', function (Blueprint $table) {
                // Índices para filtros de fecha (muy importantes para eventos)
                if (!$this->hasIndex('eventos', 'eventos_start_time_index')) {
                    $table->index('start_time'); // Ordenamiento y filtros
                }
                if (!$this->hasIndex('eventos', 'eventos_end_time_index')) {
                    $table->index('end_time'); // Scope active()
                }

                // Índices para filtros comunes
                if (!$this->hasIndex('eventos', 'eventos_is_active_index')) {
                    $table->index('is_active');
                }
                if (!$this->hasIndex('eventos', 'eventos_category_index')) {
                    $table->index('category');
                }
                if (!$this->hasIndex('eventos', 'eventos_user_id_index')) {
                    $table->index('user_id');
                }

                // Índice compuesto para listados de eventos activos
                if (!$this->hasIndex('eventos', 'eventos_active_upcoming_index')) {
                    $table->index(['is_active', 'end_time', 'start_time']); // Eventos activos ordenados
                }
            });
        }

        // ========== Tabla: experiencias ==========
        if (Schema::hasTable('experiencias')) {
            Schema::table('experiencias', function (Blueprint $table) {
                // Índices para filtros comunes
                if (!$this->hasIndex('experiencias', 'experiencias_is_active_index')) {
                    $table->index('is_active');
                }
                if (!$this->hasIndex('experiencias', 'experiencias_difficulty_index')) {
                    $table->index('difficulty'); // byDifficulty()
                }
                if (!$this->hasIndex('experiencias', 'experiencias_user_id_index')) {
                    $table->index('user_id');
                }
                if (!$this->hasIndex('experiencias', 'experiencias_locale_id_index')) {
                    $table->index('locale_id'); // JOIN con locales
                }

                // Índice para filtro de precio
                if (!$this->hasIndex('experiencias', 'experiencias_price_index')) {
                    $table->index('price_pen'); // withinBudget()
                }

                // Índice compuesto
                if (!$this->hasIndex('experiencias', 'experiencias_active_price_index')) {
                    $table->index(['is_active', 'price_pen', 'created_at']);
                }
            });
        }

        // ========== Tabla: promociones ==========
        if (Schema::hasTable('promociones')) {
            Schema::table('promociones', function (Blueprint $table) {
                // Índices para filtros de fecha (críticos para promociones)
                if (!$this->hasIndex('promociones', 'promociones_start_date_index')) {
                    $table->index('start_date');
                }
                if (!$this->hasIndex('promociones', 'promociones_end_date_index')) {
                    $table->index('end_date');
                }

                // Índices para filtros comunes
                if (!$this->hasIndex('promociones', 'promociones_is_active_index')) {
                    $table->index('is_active');
                }
                if (!$this->hasIndex('promociones', 'promociones_discount_type_index')) {
                    $table->index('discount_type'); // byType()
                }
                if (!$this->hasIndex('promociones', 'promociones_locale_id_index')) {
                    $table->index('locale_id'); // byLocale()
                }

                // Índice compuesto para promociones activas
                if (!$this->hasIndex('promociones', 'promociones_active_dates_index')) {
                    $table->index(['is_active', 'start_date', 'end_date', 'created_at']);
                }
            });
        }

        // ========== Tabla: candelaria ==========
        if (Schema::hasTable('candelaria')) {
            Schema::table('candelaria', function (Blueprint $table) {
                // Índices para filtros comunes
                if (!$this->hasIndex('candelaria', 'candelaria_is_active_index')) {
                    $table->index('is_active');
                }
                if (!$this->hasIndex('candelaria', 'candelaria_category_index')) {
                    $table->index('category');
                }
                if (!$this->hasIndex('candelaria', 'candelaria_is_featured_index')) {
                    $table->index('is_featured'); // featured()
                }

                // Índice compuesto
                if (!$this->hasIndex('candelaria', 'candelaria_active_featured_index')) {
                    $table->index(['is_active', 'is_featured', 'created_at']);
                }
            });
        }

        // ========== Tabla: reviews ==========
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                // Índices para relaciones polimórficas
                if (!$this->hasIndex('reviews', 'reviews_reviewable_index')) {
                    $table->index(['reviewable_type', 'reviewable_id']); // Relación polimórfica
                }
                if (!$this->hasIndex('reviews', 'reviews_user_id_index')) {
                    $table->index('user_id');
                }

                // Índice para rating
                if (!$this->hasIndex('reviews', 'reviews_rating_index')) {
                    $table->index('rating'); // Cálculo de promedios
                }
            });
        }

        // ========== Tabla: favorites ==========
        if (Schema::hasTable('favorites')) {
            Schema::table('favorites', function (Blueprint $table) {
                // Índices para relaciones polimórficas
                if (!$this->hasIndex('favorites', 'favorites_favoriteable_index')) {
                    $table->index(['favoriteable_type', 'favoriteable_id']);
                }
                if (!$this->hasIndex('favorites', 'favorites_user_id_index')) {
                    $table->index('user_id');
                }

                // Índice compuesto para búsquedas de favoritos
                if (!$this->hasIndex('favorites', 'favorites_user_favoriteable_index')) {
                    $table->index(['user_id', 'favoriteable_type', 'favoriteable_id']); // Check de favoritos
                }
            });
        }

        // ========== Tabla: bookings ==========
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                // Índices para filtros comunes
                if (!$this->hasIndex('bookings', 'bookings_user_id_index')) {
                    $table->index('user_id');
                }
                if (!$this->hasIndex('bookings', 'bookings_experiencia_id_index')) {
                    $table->index('experiencia_id');
                }
                if (!$this->hasIndex('bookings', 'bookings_status_index')) {
                    $table->index('status');
                }
                if (!$this->hasIndex('bookings', 'bookings_date_index')) {
                    $table->index('date');
                }

                // Índice compuesto
                if (!$this->hasIndex('bookings', 'bookings_user_status_index')) {
                    $table->index(['user_id', 'status', 'date']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índices en orden inverso

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropIndex('bookings_user_status_index');
                $table->dropIndex('bookings_date_index');
                $table->dropIndex('bookings_status_index');
                $table->dropIndex('bookings_experiencia_id_index');
                $table->dropIndex('bookings_user_id_index');
            });
        }

        if (Schema::hasTable('favorites')) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->dropIndex('favorites_user_favoriteable_index');
                $table->dropIndex('favorites_user_id_index');
                $table->dropIndex('favorites_favoriteable_index');
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropIndex('reviews_rating_index');
                $table->dropIndex('reviews_user_id_index');
                $table->dropIndex('reviews_reviewable_index');
            });
        }

        if (Schema::hasTable('candelaria')) {
            Schema::table('candelaria', function (Blueprint $table) {
                $table->dropIndex('candelaria_active_featured_index');
                $table->dropIndex('candelaria_is_featured_index');
                $table->dropIndex('candelaria_category_index');
                $table->dropIndex('candelaria_is_active_index');
            });
        }

        if (Schema::hasTable('promociones')) {
            Schema::table('promociones', function (Blueprint $table) {
                $table->dropIndex('promociones_active_dates_index');
                $table->dropIndex('promociones_locale_id_index');
                $table->dropIndex('promociones_discount_type_index');
                $table->dropIndex('promociones_is_active_index');
                $table->dropIndex('promociones_end_date_index');
                $table->dropIndex('promociones_start_date_index');
            });
        }

        if (Schema::hasTable('experiencias')) {
            Schema::table('experiencias', function (Blueprint $table) {
                $table->dropIndex('experiencias_active_price_index');
                $table->dropIndex('experiencias_price_index');
                $table->dropIndex('experiencias_locale_id_index');
                $table->dropIndex('experiencias_user_id_index');
                $table->dropIndex('experiencias_difficulty_index');
                $table->dropIndex('experiencias_is_active_index');
            });
        }

        if (Schema::hasTable('eventos')) {
            Schema::table('eventos', function (Blueprint $table) {
                $table->dropIndex('eventos_active_upcoming_index');
                $table->dropIndex('eventos_user_id_index');
                $table->dropIndex('eventos_category_index');
                $table->dropIndex('eventos_is_active_index');
                $table->dropIndex('eventos_end_time_index');
                $table->dropIndex('eventos_start_time_index');
            });
        }

        if (Schema::hasTable('locales')) {
            Schema::table('locales', function (Blueprint $table) {
                $table->dropIndex('locales_search_index');
                $table->dropIndex('locales_active_category_index');
                $table->dropIndex('locales_user_id_index');
                $table->dropIndex('locales_category_index');
                $table->dropIndex('locales_is_active_index');
            });
        }
    }

    /**
     * Verificar si un índice existe
     */
    protected function hasIndex(string $table, string $indexName): bool
    {
        try {
            $connection = Schema::getConnection();
            $schemaManager = $connection->getDoctrineSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);
            return isset($indexes[$indexName]);
        } catch (\Exception $e) {
            // Si hay error, asumir que no existe (será creado)
            return false;
        }
    }
};
