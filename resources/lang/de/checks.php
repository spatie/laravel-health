<?php

return [
    // Check titles
    'titles' => [
        'debug_mode' => 'Debug-Modus',
        'database' => 'Datenbankverbindung',
        'database_size' => 'Datenbankgröße',
        'database_connection_count' => 'Datenbankverbindungen',
        'database_table_size' => 'Datenbanktabellengröße',
        'cache' => 'Cache',
        'queue' => 'Warteschlange',
        'environment' => 'Umgebung',
        'horizon' => 'Horizon',
        'redis' => 'Redis',
        'redis_memory_usage' => 'Redis-Speicherverbrauch',
        'used_disk_space' => 'Verwendeter Speicherplatz',
        'schedule' => 'Scheduler',
        'optimized_app' => 'Optimierte Anwendung',
        'ping' => 'Ping',
        'meilisearch' => 'Meilisearch',
        'flare_error_occurrence_count' => 'Flare-Fehleranzahl',
        'backups' => 'Backups',
    ],

    'debug_mode' => [
        'expected_but_was' => 'Der Debug-Modus sollte `:expected` sein, ist aber tatsächlich `:actual`',
    ],

    'database' => [
        'connection_failed' => 'Verbindung zur Datenbank konnte nicht hergestellt werden: `:message`',
    ],

    'database_size' => [
        'size_above_threshold' => 'Die Datenbankgröße beträgt :size GB, was über dem Schwellenwert von :threshold GB liegt',
    ],

    'database_connection_count' => [
        'too_many_connections' => 'Es gibt zu viele Datenbankverbindungen (:count Verbindungen)',
    ],

    'database_table_size' => [
        'table_too_large' => 'Die Tabelle `:table` ist :size MB groß, was über dem Schwellenwert von :threshold MB liegt.',
        'sizes_ok' => 'Tabellengrößen sind in Ordnung',
        'single_table_too_big' => 'Diese Tabelle ist zu groß: :tables',
        'multiple_tables_too_big' => 'Diese Tabellen sind zu groß: :tables',
    ],

    'cache' => [
        'could_not_set_retrieve' => 'Ein Cache-Wert der Anwendung konnte nicht gesetzt oder abgerufen werden.',
        'exception_occurred' => 'Eine Ausnahme ist beim Anwendungs-Cache aufgetreten: `:message`',
    ],

    'queue' => [
        'not_run_yet' => 'Die `:queue` Warteschlange wurde noch nicht ausgeführt.',
        'too_long_ago' => 'Die letzte Ausführung der `:queue` Warteschlange war vor mehr als :minutes Minuten.',
        'jobs_failed' => 'Warteschlangen-Jobs sind fehlgeschlagen. Prüfen Sie die Meta-Daten für weitere Informationen.',
    ],

    'environment' => [
        'expected_but_was' => 'Die Umgebung sollte `:expected` sein, ist aber tatsächlich `:actual`',
    ],

    'horizon' => [
        'not_installed' => 'Horizon scheint nicht korrekt installiert zu sein.',
        'not_running' => 'Horizon läuft nicht.',
        'paused' => 'Horizon läuft, aber der Status ist pausiert.',
        'running' => 'Läuft',
        'not_running_short' => 'Läuft nicht',
        'paused_short' => 'Pausiert',
    ],

    'redis' => [
        'connection_exception' => 'Eine Ausnahme ist beim Verbinden zu Redis aufgetreten: `:message`',
        'falsy_response' => 'Redis hat eine falsche Antwort beim Verbindungsversuch zurückgegeben.',
    ],

    'redis_memory_usage' => [
        'memory_above_threshold' => 'Redis verwendet :used MB Speicher, was über dem Schwellenwert von :threshold MB liegt.',
    ],

    'used_disk_space' => [
        'almost_full_error' => 'Die Festplatte ist fast voll (:percentage% verwendet).',
        'almost_full_warning' => 'Die Festplatte ist fast voll (:percentage% verwendet).',
    ],

    'schedule' => [
        'not_running' => 'Der Scheduler wurde noch nicht ausgeführt.',
        'last_run_too_long_ago' => 'Die letzte Ausführung des Schedulers war vor mehr als :minutes Minuten.',
    ],

    'optimized_app' => [
        'config_not_cached' => 'Die Konfiguration ist nicht zwischengespeichert.',
        'routes_not_cached' => 'Die Routen sind nicht zwischengespeichert.',
        'events_not_cached' => 'Die Events sind nicht zwischengespeichert.',
    ],

    'ping' => [
        'unreachable' => 'Nicht erreichbar',
        'url_not_set' => 'URL wurde nicht gesetzt',
        'ping_failed' => 'Ping fehlgeschlagen',
    ],

    'meilisearch' => [
        'unreachable' => 'Nicht erreichbar',
        'did_not_respond' => 'Hat nicht geantwortet',
        'invalid_response' => 'Ungültige Antwort',
        'health_check_failed' => 'Der Gesundheitscheck gab einen Status `:status` zurück.',
    ],

    'flare_error_occurrence_count' => [
        'too_many_errors' => 'In den letzten :period Minuten sind :count Fehler aufgetreten.',
    ],

    'backups' => [
        'no_backups_found' => 'Keine Backups gefunden',
        'not_enough_backups' => 'Nicht genügend Backups gefunden',
        'too_many_backups' => 'Zu viele Backups gefunden',
        'youngest_backup_too_old' => 'Das neueste Backup ist zu alt',
        'oldest_backup_too_young' => 'Das älteste Backup ist zu jung',
        'backups_not_large_enough' => 'Backups sind nicht groß genug',
    ],

    'common' => [
        'true' => 'wahr',
        'false' => 'falsch',
        'running' => 'Läuft',
        'not_running' => 'Läuft nicht',
        'paused' => 'Pausiert',
        'ok' => 'OK',
        'failed' => 'Fehlgeschlagen',
        'warning' => 'Warnung',
    ],
];
