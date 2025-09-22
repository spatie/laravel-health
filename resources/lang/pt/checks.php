<?php

return array (
  'titles' => 
  array (
    'debug_mode' => 'Debug Mode',
    'database' => 'Database Connection',
    'database_size' => 'Database Size',
    'database_connection_count' => 'Database Connections',
    'database_table_size' => 'Database Table Size',
    'cache' => 'Cache',
    'queue' => 'Queue',
    'environment' => 'Environment',
    'horizon' => 'Horizon',
    'redis' => 'Redis',
    'redis_memory_usage' => 'Redis Memory Usage',
    'used_disk_space' => 'Used Disk Space',
    'schedule' => 'Schedule',
    'optimized_app' => 'Optimized App',
    'ping' => 'Ping',
    'meilisearch' => 'Meilisearch',
    'flare_error_occurrence_count' => 'Flare Error Count',
    'backups' => 'Backups',
  ),
  'debug_mode' => 
  array (
    'expected_but_was' => 'The debug mode was expected to be `:expected`, but actually was `:actual`',
  ),
  'database' => 
  array (
    'connection_failed' => 'Could not connect to the database: `:message`',
  ),
  'database_size' => 
  array (
    'size_above_threshold' => 'The database size is :size GB, which is above the threshold of :threshold GB',
  ),
  'database_connection_count' => 
  array (
    'too_many_connections' => 'There are too many database connections (:count connections)',
  ),
  'database_table_size' => 
  array (
    'table_too_large' => 'The table `:table` is :size MB in size, which is above the threshold of :threshold MB.',
    'sizes_ok' => 'Table sizes are ok',
    'single_table_too_big' => 'This table is too big: :tables',
    'multiple_tables_too_big' => 'These tables are too big: :tables',
  ),
  'cache' => 
  array (
    'could_not_set_retrieve' => 'An application cache value could not be set or retrieved.',
    'exception_occurred' => 'An exception occurred with the application cache: `:message`',
  ),
  'queue' => 
  array (
    'not_run_yet' => 'The `:queue` queue has not run yet.',
    'too_long_ago' => 'The last run of the `:queue` queue was more than :minutes minutes ago.',
    'jobs_failed' => 'Queue jobs have failed. Check the meta data for more information.',
  ),
  'environment' => 
  array (
    'expected_but_was' => 'The environment was expected to be `:expected`, but actually was `:actual`',
  ),
  'horizon' => 
  array (
    'not_installed' => 'Horizon does not seem to be installed correctly.',
    'not_running' => 'Horizon is not running.',
    'paused' => 'Horizon is running, but the status is paused.',
    'running' => 'Running',
    'not_running_short' => 'Not running',
    'paused_short' => 'Paused',
  ),
  'redis' => 
  array (
    'connection_exception' => 'An exception occurred when connecting to Redis: `:message`',
    'falsy_response' => 'Redis returned a falsy response when trying to connect to it.',
  ),
  'redis_memory_usage' => 
  array (
    'memory_above_threshold' => 'Redis is using :used MB of memory, which is above the threshold of :threshold MB.',
  ),
  'used_disk_space' => 
  array (
    'almost_full_error' => 'The disk is almost full (:percentage% used).',
    'almost_full_warning' => 'The disk is almost full (:percentage% used).',
  ),
  'schedule' => 
  array (
    'not_running' => 'The schedule has not run yet.',
    'last_run_too_long_ago' => 'The last run of the schedule was more than :minutes minutes ago.',
  ),
  'optimized_app' => 
  array (
    'config_not_cached' => 'Configs are not cached.',
    'routes_not_cached' => 'Routes are not cached.',
    'events_not_cached' => 'The events are not cached.',
  ),
  'ping' => 
  array (
    'unreachable' => 'Unreachable',
    'url_not_set' => 'URL not set',
    'ping_failed' => 'Ping failed',
  ),
  'meilisearch' => 
  array (
    'unreachable' => 'Unreachable',
    'did_not_respond' => 'Did not respond',
    'invalid_response' => 'Invalid response',
    'health_check_failed' => 'The health check returned a status `:status`.',
  ),
  'flare_error_occurrence_count' => 
  array (
    'too_many_errors' => 'In the past :period minutes, :count errors occurred.',
  ),
  'backups' => 
  array (
    'no_backups_found' => 'No backups found',
    'not_enough_backups' => 'Not enough backups found',
    'too_many_backups' => 'Too many backups found',
    'youngest_backup_too_old' => 'The youngest backup is too old',
    'oldest_backup_too_young' => 'The oldest backup is too young',
    'backups_not_large_enough' => 'Backups are not large enough',
  ),
  'common' => 
  array (
    'true' => 'true',
    'false' => 'false',
    'running' => 'Running',
    'not_running' => 'Not running',
    'paused' => 'Paused',
    'ok' => 'OK',
    'failed' => 'Failed',
    'warning' => 'Warning',
  ),
);
