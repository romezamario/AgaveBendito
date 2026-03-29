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
define( 'DB_NAME', 'promoc22_wp882' );

/** MySQL database username */
define( 'DB_USER', 'promoc22_wp882' );

/** MySQL database password */
define( 'DB_PASSWORD', '6(1]pv8d6S' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'sja7qf2yop0ongr2ronuldjh9nizf17nzgfpxnsphclowoxuokpirywbkcroyy7b' );
define( 'SECURE_AUTH_KEY',  'a5v8a2elu64bvgynnbplhshg4e29pyrcbioha9id2svrve92brkdck7g1gvp4oiu' );
define( 'LOGGED_IN_KEY',    'ucscrozyi0crmvisn7agjeupddlq3tlrxtjffoofdb1ghxddwm26covcwfiqq2s5' );
define( 'NONCE_KEY',        'igcgxap82diyxmnq9swepyuggaef9h282vwgmywovq9mif3zaussut6jwe9ied8x' );
define( 'AUTH_SALT',        'f3vvadjjzc3v0kbx6tp9x5pgtmrzubdst3ftkspawdod6rdeyin29byndckxjggz' );
define( 'SECURE_AUTH_SALT', 'ih7ldt0fw0qzoimiux7hhtpcecxa4jjotp0zkf8whnmbpd1cjycoiyosp0lainzm' );
define( 'LOGGED_IN_SALT',   'p6bhirzzkt1pcejfqgzzndvfxuk1ajqemljnp8qcl27sr2a5tedaymyx0eyn0gfk' );
define( 'NONCE_SALT',       'cvclx6i42fzgq6ewlaz9k92x6kjuvsl1judb1hmjaxdpjptcuyiq6mp1scgin77l' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp1x_';

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
define( 'WP_DEBUG', false );
// Guardamos errores en /wp-content/debug.log
define ( 'WP_DEBUG_LOG', false);
// Ocultamos errores en pantalla
define ( 'WP_DEBUG_DISPLAY', false);

define( 'DISALLOW_FILE_EDIT', true );
define( 'CONCATENATE_SCRIPTS', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
