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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'elgenios' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         ']gm !Nbis$MC{Ck%_@V#:{Z  v1]Cyh)/iU6=BGKzs@TfR+]+eMjsktG2N$++y`i' );
define( 'SECURE_AUTH_KEY',  ':nLy#c6n^lSS1EFEk^>vqQ,DN/&(~9;oic}EWdn9zvY+C(|tMExF~A*^/wi[(9iy' );
define( 'LOGGED_IN_KEY',    'cpXFBRj6g%Fa-!A3W=w1#vXIdbD;PBvK_8q@V]en?IP?GOHEZ29T;tT^UvLgWS??' );
define( 'NONCE_KEY',        'tcIh25.+Y#1Q-[d0CStqZC3su3T9 qSHhP&f?I>Tz_!A3L}1xadXU408R|%(wtNp' );
define( 'AUTH_SALT',        '>fuNMkzjXY%k^/bWeM(@o2o[:N^hI3]2.Z1Qa3gT=<1$4X0%u9uH9+aG)XO,p2)c' );
define( 'SECURE_AUTH_SALT', 'o8Ok6GMzy`5fNAk5IPd,7Kpf`;R%N0Nyi,b9s<apa-2&PWiN(E&9bZ~NE2J(*q>%' );
define( 'LOGGED_IN_SALT',   '.<xM5=uN<Rpw|?@*m/Q]pIsNxW7=#U./KRw2$_5Dj[E.-3L%HRIo)_p7btjf.K7R' );
define( 'NONCE_SALT',       '*??v7.Q=q|/jOg{7hJ#PVx!/jR*nE=WLwyHF+(H_o7h*u7LEgptrbLt{9ZJ[CN}C' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
