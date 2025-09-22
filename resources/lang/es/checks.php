<?php

return [
    // Check titles
    'titles' => [
        'debug_mode' => 'Modo Debug',
        'database' => 'Conexión Base de Datos',
        'database_size' => 'Tamaño Base de Datos',
        'database_connection_count' => 'Conexiones Base de Datos',
        'database_table_size' => 'Tamaño Tabla Base de Datos',
        'cache' => 'Caché',
        'queue' => 'Cola',
        'environment' => 'Entorno',
        'horizon' => 'Horizon',
        'redis' => 'Redis',
        'redis_memory_usage' => 'Uso Memoria Redis',
        'used_disk_space' => 'Espacio Disco Usado',
        'schedule' => 'Programador',
        'optimized_app' => 'Aplicación Optimizada',
        'ping' => 'Ping',
        'meilisearch' => 'Meilisearch',
        'flare_error_occurrence_count' => 'Recuento Errores Flare',
        'backups' => 'Copias de Seguridad',
    ],

    'debug_mode' => [
        'expected_but_was' => 'Se esperaba que el modo debug fuera `:expected`, pero en realidad era `:actual`',
    ],

    'database' => [
        'connection_failed' => 'No se pudo conectar a la base de datos: `:message`',
    ],

    'database_size' => [
        'size_above_threshold' => 'El tamaño de la base de datos es :size GB, que está por encima del umbral de :threshold GB',
    ],

    'database_connection_count' => [
        'too_many_connections' => 'Hay demasiadas conexiones a la base de datos (:count conexiones)',
    ],

    'database_table_size' => [
        'table_too_large' => 'La tabla `:table` tiene :size MB de tamaño, que está por encima del umbral de :threshold MB.',
        'sizes_ok' => 'Los tamaños de las tablas están bien',
        'single_table_too_big' => 'Esta tabla es demasiado grande: :tables',
        'multiple_tables_too_big' => 'Estas tablas son demasiado grandes: :tables',
    ],

    'cache' => [
        'could_not_set_retrieve' => 'No se pudo establecer o recuperar un valor de caché de la aplicación.',
        'exception_occurred' => 'Ocurrió una excepción con el caché de la aplicación: `:message`',
    ],

    'queue' => [
        'not_run_yet' => 'La cola `:queue` aún no se ha ejecutado.',
        'too_long_ago' => 'La última ejecución de la cola `:queue` fue hace más de :minutes minutos.',
        'jobs_failed' => 'Los trabajos de la cola han fallado. Verifique los metadatos para más información.',
    ],

    'environment' => [
        'expected_but_was' => 'Se esperaba que el entorno fuera `:expected`, pero en realidad era `:actual`',
    ],

    'horizon' => [
        'not_installed' => 'Horizon no parece estar instalado correctamente.',
        'not_running' => 'Horizon no se está ejecutando.',
        'paused' => 'Horizon se está ejecutando, pero el estado está pausado.',
        'running' => 'Ejecutándose',
        'not_running_short' => 'No ejecutándose',
        'paused_short' => 'Pausado',
    ],

    'redis' => [
        'connection_exception' => 'Ocurrió una excepción al conectar con Redis: `:message`',
        'falsy_response' => 'Redis devolvió una respuesta falsa al intentar conectarse.',
    ],

    'redis_memory_usage' => [
        'memory_above_threshold' => 'Redis está usando :used MB de memoria, que está por encima del umbral de :threshold MB.',
    ],

    'used_disk_space' => [
        'almost_full_error' => 'El disco está casi lleno (:percentage% usado).',
        'almost_full_warning' => 'El disco está casi lleno (:percentage% usado).',
    ],

    'schedule' => [
        'not_running' => 'El programador aún no se ha ejecutado.',
        'last_run_too_long_ago' => 'La última ejecución del programador fue hace más de :minutes minutos.',
    ],

    'optimized_app' => [
        'config_not_cached' => 'Las configuraciones no están en caché.',
        'routes_not_cached' => 'Las rutas no están en caché.',
        'events_not_cached' => 'Los eventos no están en caché.',
    ],

    'ping' => [
        'unreachable' => 'Inalcanzable',
        'url_not_set' => 'URL no establecida',
        'ping_failed' => 'Ping falló',
    ],

    'meilisearch' => [
        'unreachable' => 'Inalcanzable',
        'did_not_respond' => 'No respondió',
        'invalid_response' => 'Respuesta inválida',
        'health_check_failed' => 'La verificación de salud devolvió un estado `:status`.',
    ],

    'flare_error_occurrence_count' => [
        'too_many_errors' => 'En los últimos :period minutos, ocurrieron :count errores.',
    ],

    'backups' => [
        'no_backups_found' => 'No se encontraron copias de seguridad',
        'not_enough_backups' => 'No se encontraron suficientes copias de seguridad',
        'too_many_backups' => 'Se encontraron demasiadas copias de seguridad',
        'youngest_backup_too_old' => 'La copia de seguridad más reciente es demasiado antigua',
        'oldest_backup_too_young' => 'La copia de seguridad más antigua es demasiado reciente',
        'backups_not_large_enough' => 'Las copias de seguridad no son lo suficientemente grandes',
    ],

    'common' => [
        'true' => 'verdadero',
        'false' => 'falso',
        'running' => 'Ejecutándose',
        'not_running' => 'No ejecutándose',
        'paused' => 'Pausado',
        'ok' => 'OK',
        'failed' => 'Falló',
        'warning' => 'Advertencia',
    ],
];
