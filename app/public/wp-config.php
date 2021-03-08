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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '6+D4FePpS5kQ6lCv7wGvGUJRmGNSoD5DC7p+KaQ40UuXCttQA0lwZrq2RMSKYb8iYSrgWy/9JbT44uU301j3dg==');
define('SECURE_AUTH_KEY',  'hFXfz1nOcD5c6l9DbHMmWyRAe+1IdGp70lqD3dTOW6ZojSkWoMT2buW77LQ+33Sx9rSG1r42cdTv03ZtmU2WDw==');
define('LOGGED_IN_KEY',    'xeLKv//w7bH9adPU/MC89bpDbr8WrJ/UtkTXy/pD7pmW+JZ7+TV6k0LsgHX/RHZK0+1x5dmggluvaUPE2wFaxA==');
define('NONCE_KEY',        'ViW2YkJwcMmzPcNvo8wiljsSHtovuiBkKFVe3JNh7XS5T46Nuelu4uXYw4x7kAkcr4k8qIFsfmAjWEKn9uA/7Q==');
define('AUTH_SALT',        'Zwk3EqdLkGC3NcjXeKk2+8RdHCyNdsQTOPEw9cD2X1IYx6ivmqo81ZUsyMWlbynk7Kb+V0nXX5Z/ZJbD5iSGkw==');
define('SECURE_AUTH_SALT', 'tAKA2b6cORL5XKtuU1b4pVO3aFL8bIs3ObgfnOuwI0ckyPFgwpCl8XEm/m+Fd8KQ8MCT2zweIKL+UVLhZTNv8A==');
define('LOGGED_IN_SALT',   '2Hv7zElmnjEEoVrD5mBHmxlhoIwsLVT4KEaGML8RKSO9Dk4otcpEZwETKEg5huGRiCvKLc9Y+Gfih9pGMC3YFw==');
define('NONCE_SALT',       'n06Jf4K+4DJQMOAxX/4+aZTq9mJd9lq+NAip2xHFEhOotxhQs9DrAsaf8E5EdCHLWU8h3ImXMxaVG6a765AcSw==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
