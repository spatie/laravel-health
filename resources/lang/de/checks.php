<?php

return [
    'debug_mode' => [
        'expected_but_was' => 'Der Debug-Modus sollte `:expected` sein, war aber tatsächlich `:actual`',
    ],

    'database' => [
        'connection_failed' => 'Verbindung zur Datenbank konnte nicht hergestellt werden: `:message`',
    ],

    'database_size' => [
        'size_above_threshold' => 'Die Datenbankgröße beträgt :size GB, was über dem Schwellenwert von :threshold GB liegt',
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
        'expected_but_was' => 'Die Umgebung sollte `:expected` sein, war aber tatsächlich `:actual`',
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

    'used_disk_space' => [
        'almost_full_error' => 'Die Festplatte ist fast voll (:percentage% verwendet).',
        'almost_full_warning' => 'Die Festplatte ist fast voll (:percentage% verwendet).',
    ],

    'schedule' => [
        'not_running' => 'Der Scheduler wurde noch nicht ausgeführt.',
        'last_run_too_long_ago' => 'Die letzte Ausführung des Schedulers war vor mehr als :minutes Minuten.',
    ],

    'redis_memory_usage' => [
        'memory_above_threshold' => 'Redis verwendet :used MB Speicher, was über dem Schwellenwert von :threshold MB liegt.',
    ],

    'database_connection_count' => [
        'too_many_connections' => 'Zu viele Datenbankverbindungen: :count (Maximum: :max)',
    ],

    'database_table_size' => [
        'table_too_large' => 'Die Tabelle `:table` ist :size MB groß, was über dem Schwellenwert von :threshold MB liegt.',
    ],

    'optimized_app' => [
        'config_not_cached' => 'Die Konfiguration ist nicht zwischengespeichert.',
        'routes_not_cached' => 'Die Routen sind nicht zwischengespeichert.',
        'events_not_cached' => 'Die Events sind nicht zwischengespeichert.',
    ],

    'ping' => [
        'unreachable' => 'Die URL `:url` ist nicht erreichbar.',
        'timeout' => 'Zeitüberschreitung beim Erreichen der URL `:url`.',
        'exception' => 'Ausnahme beim Pingen der URL `:url`: `:message`',
    ],

    'meilisearch' => [
        'unreachable' => 'Meilisearch ist unter `:url` nicht erreichbar.',
        'exception' => 'Ausnahme beim Verbinden zu Meilisearch: `:message`',
    ],

    'flare_error_occurrence_count' => [
        'too_many_errors' => 'Zu viele Flare-Fehler in den letzten :period Minuten: :count (Maximum: :max)',
    ],

    'backups' => [
        'no_backups_found' => 'Keine Backups gefunden.',
        'backup_too_old' => 'Das neueste Backup ist :age Stunden alt, was älter als :max Stunden ist.',
        'backup_too_small' => 'Das neueste Backup ist nur :size MB groß, was kleiner als :min MB ist.',
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
