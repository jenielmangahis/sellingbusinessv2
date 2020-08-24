<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'sellingb_beta');

/** MySQL database username */
define('DB_USER', 'sellingb_betauser');

/** MySQL database password */
define('DB_PASSWORD', ';,F+GG3M~eon');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'YGUZwPUx4YbVsyfDuUZm92UZM85mJFT8rHraUEVBmy9ZLqZVFY1OmUY5uvgQ6uXE');
define('SECURE_AUTH_KEY',  'RvVtt8mpKT7iMy3WgAbOQ0WUvksoMMP5kJaDhO0XJSiVTS5nwnUJTAVsydsYtVII');
define('LOGGED_IN_KEY',    'zGM9HKUyYR0SV8H5ce38AmtqvYXPIePCKxT7et662apbMNlLnaiEUaQkwj4MTo4F');
define('NONCE_KEY',        'qQijl1VGCcr7HsRET3gfX1GtnMKhSHYVL1q5GtHX6KwNJbp2UIpmzkn7WSpNB4lK');
define('AUTH_SALT',        'ijAggULV02VnNPL9ERlisdwUsOws3kub7DFRg06U321jK6RoFt6HaUhdCwM6LPo0');
define('SECURE_AUTH_SALT', 'cDHt4Sb9SxtczmDIVbXNIxkT1p6yyPdTnSXupahNerh8twNnKNdNBKMvNgH2Fpjt');
define('LOGGED_IN_SALT',   '5Z84HSM3YTi1bR34kY7DENhFHXXFC47QNLn2HtxEiLAlbvzuTQNHgXKcCAl5RacY');
define('NONCE_SALT',       '1iVT0ZHwWAyL7JwlkikFsD9Ac8TAK3kF9ZsaGQPOBuODHjTuWqZwWx8SxQ8xtMf2');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
