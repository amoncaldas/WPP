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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'admin');

/** MySQL hostname */
define('DB_HOST', 'mysql');

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
define('AUTH_KEY',         '185f8d10fb20931ec4a19a1d16b7ee600514c820');
define('SECURE_AUTH_KEY',  '5429f124bd1b86739017c41e9e698f61f6be5357');
define('LOGGED_IN_KEY',    'fb8e71d888e5c47dc7ef41575bee7f4d3849b89c');
define('NONCE_KEY',        '0aa8ae8e5b48b9f6dc9a999b6a619d1845e63508');
define('AUTH_SALT',        'ac1eb19a5fe8c463fb1ccf9ac43c0de1704e1f38');
define('SECURE_AUTH_SALT', 'ce4f5c1d3319d7525bbabcc3a80f6055be9be0fe');
define('LOGGED_IN_SALT',   '155f434fac6e959687136d5d76d1af2dc4fe40cc');
define('NONCE_SALT',       '56a986f1b3d360e97b04836f5311efd7cbea9a69');

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
$debug = getenv('ENABLE_DEBUG', true) ?: getenv('ENABLE_DEBUG');
define('WP_DEBUG', $debug === "true");
define( 'WP_DEBUG_LOG', $debug === "true" );
define( 'SCRIPT_DEBUG', $debug === "true" );

/**
 * The error reporting can be logged by WordPress
 * using WP_DEBUG_LOG constant based in the environment variable DEBUG_SUPPORT
 * so we disable display all errors and warnings 
 * @see https://codex.wordpress.org/Debugging_in_WordPress#WP_DEBUG_DISPLAY
 */
define('WP_DEBUG_DISPLAY', false); 
@ini_set('display_errors','Off');
@ini_set('error_reporting', 0 );


/** JWT authentication for wp-rest api */
define('JWT_AUTH_SECRET_KEY', 'He7yRIA!AKUOoj3I`=QzKZ|077~YVs7W2A-!`Vb?z~/|[0xfPXQIm$2)ICA(VN&]');
define('JWT_AUTH_CORS_ENABLE', true);

/* Multi-site */
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', getenv('SITE_URL'));
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define( 'WP_POST_REVISIONS', 20 );

/**
 * WordPress store in db absolute urls and this is the way to update this
 * @see https://codex.wordpress.org/Changing_The_Site_URL
 * so we safely get the value of an environment variable, ignoring whether
 * or not it was set by a SAPI or has been changed with putenv
 */
$site_url = getenv('SITE_URL', true) ?: getenv('SITE_URL');
if(isset($site_url)) {
	define('WP_HOME',$site_url);
	define('WP_SITEURL',$site_url);
}


// If we're behind a proxy server and using HTTPS, we need to alert Wordpress of that fact
// see also http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
