<?php 
/*plugin configs*/
global $wpdb, $table_name, $kz_db_version;
define('KZ_DB_VERSION', '1.2.5');
define('TABLE_NAME', $wpdb->prefix . 'cwa');
define('CHARSET_COLLATE', '');
$table_name = TABLE_NAME;
$kz_db_version = KZ_DB_VERSION;

?>