<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '.$,po+=`lZ:%y+)pxpn!=YwW4S; A[u5vHNcp<d({q]5#5MI)>8?T)JkU6DT}eWN' );
define( 'SECURE_AUTH_KEY',  ')6e9{OO(CxylG=o4=Ifa4A,z2Q=:V[SAF**+U8;J,X%XqpW{:N__BhPG[*+@R~9w' );
define( 'LOGGED_IN_KEY',    ',rNl:yQCihGWXBd&C(2Kx&>CFeVAfw=,>?%p]=)H%AmT@-]`EU:&k4Lz0@ ,/:7y' );
define( 'NONCE_KEY',        's29BdB~^s/:GuZ`^!(N[%-acaG|wPgDC0oAB>m6&GCWtGTV9U2sH!9Pc1@Rl/f]L' );
define( 'AUTH_SALT',        'oW,-]<<JJi19Scf.vnXbv~{e{T:=29&h~<1NNMKOM~HVCcGM?OEGo8zP?%Z-,o_S' );
define( 'SECURE_AUTH_SALT', '{Z?%|;BQB},m%2B_A~I1o#M9VLRo`m5Ow?ou=)N<mqZ05Da(=_U9~+T1AyW:xX_4' );
define( 'LOGGED_IN_SALT',   'G6>-G:UdO <RYYkmi;:l(dgyi+TotOw4%PTkk_}ao`-Ew04n S]!Az0,mOSv _#U' );
define( 'NONCE_SALT',       'mz{mPwr0,2knRcyRgh`}$@,a*D83Tt_/F)i-F;3LO3/ 3bgv/m|c<C,WK*90??XF' );
define( 'FS_METHOD', 'direct' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
