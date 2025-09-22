<?php

return [
    // Check titles
    'titles' => [
        'debug_mode' => 'Mode Debug',
        'database' => 'Connexion Base de Données',
        'database_size' => 'Taille Base de Données',
        'database_connection_count' => 'Connexions Base de Données',
        'database_table_size' => 'Taille Table Base de Données',
        'cache' => 'Cache',
        'queue' => 'File d\'attente',
        'environment' => 'Environnement',
        'horizon' => 'Horizon',
        'redis' => 'Redis',
        'redis_memory_usage' => 'Utilisation Mémoire Redis',
        'used_disk_space' => 'Espace Disque Utilisé',
        'schedule' => 'Planificateur',
        'optimized_app' => 'Application Optimisée',
        'ping' => 'Ping',
        'meilisearch' => 'Meilisearch',
        'flare_error_occurrence_count' => 'Nombre d\'Erreurs Flare',
        'backups' => 'Sauvegardes',
    ],

    'debug_mode' => [
        'expected_but_was' => 'Le mode debug devait être `:expected`, mais était en fait `:actual`',
    ],

    'database' => [
        'connection_failed' => 'Impossible de se connecter à la base de données : `:message`',
    ],

    'database_size' => [
        'size_above_threshold' => 'La taille de la base de données est de :size GB, ce qui dépasse le seuil de :threshold GB',
    ],

    'database_connection_count' => [
        'too_many_connections' => 'Il y a trop de connexions à la base de données (:count connexions)',
    ],

    'database_table_size' => [
        'table_too_large' => 'La table `:table` fait :size MB, ce qui dépasse le seuil de :threshold MB.',
        'sizes_ok' => 'Les tailles des tables sont correctes',
        'single_table_too_big' => 'Cette table est trop volumineuse : :tables',
        'multiple_tables_too_big' => 'Ces tables sont trop volumineuses : :tables',
    ],

    'cache' => [
        'could_not_set_retrieve' => 'Une valeur de cache de l\'application n\'a pas pu être définie ou récupérée.',
        'exception_occurred' => 'Une exception s\'est produite avec le cache de l\'application : `:message`',
    ],

    'queue' => [
        'not_run_yet' => 'La file d\'attente `:queue` n\'a pas encore été exécutée.',
        'too_long_ago' => 'La dernière exécution de la file d\'attente `:queue` remonte à plus de :minutes minutes.',
        'jobs_failed' => 'Les tâches de la file d\'attente ont échoué. Vérifiez les métadonnées pour plus d\'informations.',
    ],

    'environment' => [
        'expected_but_was' => 'L\'environnement devait être `:expected`, mais était en fait `:actual`',
    ],

    'horizon' => [
        'not_installed' => 'Horizon ne semble pas être installé correctement.',
        'not_running' => 'Horizon n\'est pas en cours d\'exécution.',
        'paused' => 'Horizon est en cours d\'exécution, mais le statut est en pause.',
        'running' => 'En cours',
        'not_running_short' => 'Arrêté',
        'paused_short' => 'En pause',
    ],

    'redis' => [
        'connection_exception' => 'Une exception s\'est produite lors de la connexion à Redis : `:message`',
        'falsy_response' => 'Redis a renvoyé une réponse fausse lors de la tentative de connexion.',
    ],

    'redis_memory_usage' => [
        'memory_above_threshold' => 'Redis utilise :used MB de mémoire, ce qui dépasse le seuil de :threshold MB.',
    ],

    'used_disk_space' => [
        'almost_full_error' => 'Le disque est presque plein (:percentage% utilisé).',
        'almost_full_warning' => 'Le disque est presque plein (:percentage% utilisé).',
    ],

    'schedule' => [
        'not_running' => 'Le planificateur n\'a pas encore été exécuté.',
        'last_run_too_long_ago' => 'La dernière exécution du planificateur remonte à plus de :minutes minutes.',
    ],

    'optimized_app' => [
        'config_not_cached' => 'Les configurations ne sont pas mises en cache.',
        'routes_not_cached' => 'Les routes ne sont pas mises en cache.',
        'events_not_cached' => 'Les événements ne sont pas mis en cache.',
    ],

    'ping' => [
        'unreachable' => 'Inaccessible',
        'url_not_set' => 'URL non définie',
        'ping_failed' => 'Ping échoué',
    ],

    'meilisearch' => [
        'unreachable' => 'Inaccessible',
        'did_not_respond' => 'N\'a pas répondu',
        'invalid_response' => 'Réponse invalide',
        'health_check_failed' => 'Le contrôle de santé a renvoyé un statut `:status`.',
    ],

    'flare_error_occurrence_count' => [
        'too_many_errors' => 'Au cours des :period dernières minutes, :count erreurs se sont produites.',
    ],

    'backups' => [
        'no_backups_found' => 'Aucune sauvegarde trouvée',
        'not_enough_backups' => 'Pas assez de sauvegardes trouvées',
        'too_many_backups' => 'Trop de sauvegardes trouvées',
        'youngest_backup_too_old' => 'La sauvegarde la plus récente est trop ancienne',
        'oldest_backup_too_young' => 'La sauvegarde la plus ancienne est trop récente',
        'backups_not_large_enough' => 'Les sauvegardes ne sont pas assez volumineuses',
    ],

    'common' => [
        'true' => 'vrai',
        'false' => 'faux',
        'running' => 'En cours',
        'not_running' => 'Arrêté',
        'paused' => 'En pause',
        'ok' => 'OK',
        'failed' => 'Échoué',
        'warning' => 'Avertissement',
    ],
];
