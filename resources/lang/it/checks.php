<?php

return array (
  'titles' => 
  array (
    'debug_mode' => 'Modalità Debug',
    'database' => 'Connessione Database',
    'database_size' => 'Dimensione Database',
    'database_connection_count' => 'Connessioni Database',
    'database_table_size' => 'Dimensione Tabella Database',
    'cache' => 'Cache',
    'queue' => 'Coda',
    'environment' => 'Ambiente',
    'horizon' => 'Horizon',
    'redis' => 'Redis',
    'redis_memory_usage' => 'Utilizzo Memoria Redis',
    'used_disk_space' => 'Spazio Disco Utilizzato',
    'schedule' => 'Pianificatore',
    'optimized_app' => 'App Ottimizzata',
    'ping' => 'Ping',
    'meilisearch' => 'Meilisearch',
    'flare_error_occurrence_count' => 'Conteggio Errori Flare',
    'backups' => 'Backup',
  ),
  'debug_mode' => 
  array (
    'expected_but_was' => 'La modalità debug doveva essere `:expected`, ma in realtà era `:actual`',
  ),
  'database' => 
  array (
    'connection_failed' => 'Impossibile connettersi al database: `:message`',
  ),
  'database_size' => 
  array (
    'size_above_threshold' => 'La dimensione del database è :size GB, che è sopra la soglia di :threshold GB',
  ),
  'database_connection_count' => 
  array (
    'too_many_connections' => 'Ci sono troppe connessioni al database (:count connessioni)',
  ),
  'database_table_size' => 
  array (
    'table_too_large' => 'La tabella `:table` è :size MB, che è sopra la soglia di :threshold MB.',
    'sizes_ok' => 'Le dimensioni delle tabelle sono ok',
    'single_table_too_big' => 'Questa tabella è troppo grande: :tables',
    'multiple_tables_too_big' => 'Queste tabelle sono troppo grandi: :tables',
  ),
  'cache' => 
  array (
    'could_not_set_retrieve' => 'Un valore cache dell\'applicazione non può essere impostato o recuperato.',
    'exception_occurred' => 'Si è verificata un\'eccezione con la cache dell\'applicazione: `:message`',
  ),
  'queue' => 
  array (
    'not_run_yet' => 'La coda `:queue` non è ancora stata eseguita.',
    'too_long_ago' => 'L\'ultima esecuzione della coda `:queue` è stata più di :minutes minuti fa.',
    'jobs_failed' => 'I lavori della coda sono falliti. Controlla i metadati per maggiori informazioni.',
  ),
  'environment' => 
  array (
    'expected_but_was' => 'L\'ambiente doveva essere `:expected`, ma in realtà era `:actual`',
  ),
  'horizon' => 
  array (
    'not_installed' => 'Horizon non sembra essere installato correttamente.',
    'not_running' => 'Horizon non è in esecuzione.',
    'paused' => 'Horizon è in esecuzione, ma lo stato è in pausa.',
    'running' => 'In esecuzione',
    'not_running_short' => 'Non in esecuzione',
    'paused_short' => 'In pausa',
  ),
  'redis' => 
  array (
    'connection_exception' => 'Si è verificata un\'eccezione durante la connessione a Redis: `:message`',
    'falsy_response' => 'Redis ha restituito una risposta falsa quando si è tentato di connettersi.',
  ),
  'redis_memory_usage' => 
  array (
    'memory_above_threshold' => 'Redis sta usando :used MB di memoria, che è sopra la soglia di :threshold MB.',
  ),
  'used_disk_space' => 
  array (
    'almost_full_error' => 'Il disco è quasi pieno (:percentage% utilizzato).',
    'almost_full_warning' => 'Il disco è quasi pieno (:percentage% utilizzato).',
  ),
  'schedule' => 
  array (
    'not_running' => 'Il pianificatore non è ancora stato eseguito.',
    'last_run_too_long_ago' => 'L\'ultima esecuzione del pianificatore è stata più di :minutes minuti fa.',
  ),
  'optimized_app' => 
  array (
    'config_not_cached' => 'Le configurazioni non sono in cache.',
    'routes_not_cached' => 'Le rotte non sono in cache.',
    'events_not_cached' => 'Gli eventi non sono in cache.',
  ),
  'ping' => 
  array (
    'unreachable' => 'Irraggiungibile',
    'url_not_set' => 'URL non impostato',
    'ping_failed' => 'Ping fallito',
  ),
  'meilisearch' => 
  array (
    'unreachable' => 'Irraggiungibile',
    'did_not_respond' => 'Non ha risposto',
    'invalid_response' => 'Risposta non valida',
    'health_check_failed' => 'Il controllo di salute ha restituito uno stato `:status`.',
  ),
  'flare_error_occurrence_count' => 
  array (
    'too_many_errors' => 'Negli ultimi :period minuti, si sono verificati :count errori.',
  ),
  'backups' => 
  array (
    'no_backups_found' => 'Nessun backup trovato',
    'not_enough_backups' => 'Non abbastanza backup trovati',
    'too_many_backups' => 'Troppi backup trovati',
    'youngest_backup_too_old' => 'Il backup più recente è troppo vecchio',
    'oldest_backup_too_young' => 'Il backup più vecchio è troppo recente',
    'backups_not_large_enough' => 'I backup non sono abbastanza grandi',
  ),
  'common' => 
  array (
    'true' => 'vero',
    'false' => 'falso',
    'running' => 'In esecuzione',
    'not_running' => 'Non in esecuzione',
    'paused' => 'In pausa',
    'ok' => 'OK',
    'failed' => 'Fallito',
    'warning' => 'Avviso',
  ),
);
