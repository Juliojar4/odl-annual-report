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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

/** Database hostname */
define( 'DB_HOST', 'database' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'Ptq%eEz8a&Ja=p.(sd9$XdaP&p7<XBd_w=cbH1G0|IdmuCC[n$L<4vf)]C6WlX_C' );
define( 'SECURE_AUTH_KEY',   'AYmWlA*}.;n{YHaQA}VAg:3T,CU@fKJTxm_hOQ!gQHK0Bo6b4}^*iOK?c@#!:]p:' );
define( 'LOGGED_IN_KEY',     'g)R_Lg~BTNW}2B(@WFb+iR;0m ]mpQ) HEQTz#7ErS@c`=3 A2mQq^lIVCsbq_O&' );
define( 'NONCE_KEY',         '%X5diDfrUR=R&w$_wQSd9.3+^nBtbFZ4tC.w|Hzz1d!;q}QMrF6l9|;w5`],oNkg' );
define( 'AUTH_SALT',         'z#7l)OHJ=3^6KH~kE8w!{o;/J:JV+(~;i&HC.GB0|._I^8dV54(bo@&h2~D~|K`x' );
define( 'SECURE_AUTH_SALT',  '#R[01T{!ub1q!LqF{0^EEBk`5M rCl:^eOKt{1MD:#n)t;c;bifF1K0@Xk,tf[g>' );
define( 'LOGGED_IN_SALT',    'GfU)vNa?Z-`ik&S-{pUo@9p6y-~Ak!~>ZwHR_9e6nTi-L{`}>U6N &NEFLqU(_d^' );
define( 'NONCE_SALT',        'O`.Q|RcJ@hznYu.vGMzsX]Q~1qiMaG6~#bziIKJirb;XAWW-9nj;Ek(4FEN|hk,j' );
define( 'WP_CACHE_KEY_SALT', 'T2?ik}Th]h+prX ;w[@~U-%9|@#E>O,k-)(H$c+k_92:DK`Y2c+)06QJ8#Tr}HBG' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
