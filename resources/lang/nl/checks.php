<?php

return array (
  'titles' => 
  array (
    'debug_mode' => 'Debug Modus',
    'database' => 'Database Verbinding',
    'database_size' => 'Database Grootte',
    'database_connection_count' => 'Database Verbindingen',
    'database_table_size' => 'Database Tabel Grootte',
    'cache' => 'Cache',
    'queue' => 'Wachtrij',
    'environment' => 'Omgeving',
    'horizon' => 'Horizon',
    'redis' => 'Redis',
    'redis_memory_usage' => 'Redis Geheugengebruik',
    'used_disk_space' => 'Gebruikte Schijfruimte',
    'schedule' => 'Planning',
    'optimized_app' => 'Geoptimaliseerde App',
    'ping' => 'Ping',
    'meilisearch' => 'Meilisearch',
    'flare_error_occurrence_count' => 'Flare Fout Aantal',
    'backups' => 'Back-ups',
  ),
  'debug_mode' => 
  array (
    'expected_but_was' => 'De debug modus werd verwacht `:expected` te zijn, maar was eigenlijk `:actual`',
  ),
  'database' => 
  array (
    'connection_failed' => 'Kon geen verbinding maken met de database: `:message`',
  ),
  'database_size' => 
  array (
    'size_above_threshold' => 'De database grootte is :size GB, wat boven de drempel van :threshold GB ligt',
  ),
  'database_connection_count' => 
  array (
    'too_many_connections' => 'Er zijn te veel database verbindingen (:count verbindingen)',
  ),
  'database_table_size' => 
  array (
    'table_too_large' => 'De tabel `:table` is :size MB groot, wat boven de drempel van :threshold MB ligt.',
    'sizes_ok' => 'Tabel groottes zijn oké',
    'single_table_too_big' => 'Deze tabel is te groot: :tables',
    'multiple_tables_too_big' => 'Deze tabellen zijn te groot: :tables',
  ),
  'cache' => 
  array (
    'could_not_set_retrieve' => 'Een applicatie cache waarde kon niet worden ingesteld of opgehaald.',
    'exception_occurred' => 'Er is een uitzondering opgetreden met de applicatie cache: `:message`',
  ),
  'queue' => 
  array (
    'not_run_yet' => 'De `:queue` wachtrij is nog niet uitgevoerd.',
    'too_long_ago' => 'De laatste uitvoering van de `:queue` wachtrij was meer dan :minutes minuten geleden.',
    'jobs_failed' => 'Wachtrij taken zijn mislukt. Controleer de metadata voor meer informatie.',
  ),
  'environment' => 
  array (
    'expected_but_was' => 'De omgeving werd verwacht `:expected` te zijn, maar was eigenlijk `:actual`',
  ),
  'horizon' => 
  array (
    'not_installed' => 'Horizon lijkt niet correct geïnstalleerd te zijn.',
    'not_running' => 'Horizon draait niet.',
    'paused' => 'Horizon draait, maar de status is gepauzeerd.',
    'running' => 'Draait',
    'not_running_short' => 'Draait niet',
    'paused_short' => 'Gepauzeerd',
  ),
  'redis' => 
  array (
    'connection_exception' => 'Er is een uitzondering opgetreden bij het verbinden met Redis: `:message`',
    'falsy_response' => 'Redis gaf een valse reactie bij het proberen te verbinden.',
  ),
  'redis_memory_usage' => 
  array (
    'memory_above_threshold' => 'Redis gebruikt :used MB geheugen, wat boven de drempel van :threshold MB ligt.',
  ),
  'used_disk_space' => 
  array (
    'almost_full_error' => 'De schijf is bijna vol (:percentage% gebruikt).',
    'almost_full_warning' => 'De schijf is bijna vol (:percentage% gebruikt).',
  ),
  'schedule' => 
  array (
    'not_running' => 'De planning is nog niet uitgevoerd.',
    'last_run_too_long_ago' => 'De laatste uitvoering van de planning was meer dan :minutes minuten geleden.',
  ),
  'optimized_app' => 
  array (
    'config_not_cached' => 'Configuraties zijn niet gecached.',
    'routes_not_cached' => 'Routes zijn niet gecached.',
    'events_not_cached' => 'De events zijn niet gecached.',
  ),
  'ping' => 
  array (
    'unreachable' => 'Onbereikbaar',
    'url_not_set' => 'URL niet ingesteld',
    'ping_failed' => 'Ping mislukt',
  ),
  'meilisearch' => 
  array (
    'unreachable' => 'Onbereikbaar',
    'did_not_respond' => 'Reageerde niet',
    'invalid_response' => 'Ongeldige reactie',
    'health_check_failed' => 'De gezondheidscontrole gaf een status `:status` terug.',
  ),
  'flare_error_occurrence_count' => 
  array (
    'too_many_errors' => 'In de afgelopen :period minuten zijn :count fouten opgetreden.',
  ),
  'backups' => 
  array (
    'no_backups_found' => 'Geen back-ups gevonden',
    'not_enough_backups' => 'Niet genoeg back-ups gevonden',
    'too_many_backups' => 'Te veel back-ups gevonden',
    'youngest_backup_too_old' => 'De nieuwste back-up is te oud',
    'oldest_backup_too_young' => 'De oudste back-up is te jong',
    'backups_not_large_enough' => 'Back-ups zijn niet groot genoeg',
  ),
  'common' => 
  array (
    'true' => 'waar',
    'false' => 'onwaar',
    'running' => 'Draait',
    'not_running' => 'Draait niet',
    'paused' => 'Gepauzeerd',
    'ok' => 'OK',
    'failed' => 'Mislukt',
    'warning' => 'Waarschuwing',
  ),
);
