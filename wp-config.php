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


/* WP Force Refresh defaults */
define( 'WP_FORCE_REFRESH_OPTION_REFRESH_INTERVAL_IN_SECONDS_DEFAULT', 3 );
define( 'WP_FORCE_REFRESH_OPTION_REFRESH_INTERVAL_IN_SECONDS', 3 );

define('Twilio_Account_ID','AC1f403e0ebcea4188a343c1337aa913e7');
define('Twilio_Auth_Token','b9f4fce4250e4e83d8c10887b75ab228');
define('Twilio_Phone_Number','19782880383');
define('Game_Timer',300);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_inspectorlocal' );

/** MySQL database username */
define( 'DB_USER', 'wp_inspectorlocal' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wp_inspectorlocal' );

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
define( 'AUTH_KEY',         'z0_br] zjAs6ba~z THRFl6O>!b6sJ0cOdh-(F&ihMXw]b.3ST0.=z0HcI1{GmEX' );
define( 'SECURE_AUTH_KEY',  '!Q0lN!@0e0{$HEkA5dreEmtxFG[43/UB`g9OBH/eG{7vb}/rWL#%oeAieQw>*_1&' );
define( 'LOGGED_IN_KEY',    '%JT-7D^$N`0.ScGoUl^/U=8j$}9$n!Ww}>sau?I:#Eo_/%[Xd%~k{:F*$ZZD;Y$w' );
define( 'NONCE_KEY',        'EZH=#[Q]dpWxS/ TOAzcPE]G]`^@5B~)Daaco8Lo=r}~Ud-l;HE>z~@wA@Kv^APT' );
define( 'AUTH_SALT',        '@]W;nC3U21e!:Ro.?:V==BD9[u?iPk6?/4e%9vo:7(*WX6/aKPG v.jDFiRsdx5M' );
define( 'SECURE_AUTH_SALT', 'cXHl1>4rv_dq,G;3,xH]22&i{z]n?W^O1vO(;Rv&*GVT*C+o/mw&N7wx5oLVXC4O' );
define( 'LOGGED_IN_SALT',   '%tyqI+&k(3cWdYEt!:EEs8Egt:pybUCzJ!hklro?HpLN$@6+V.n:=wm](Bh6ul[c' );
define( 'NONCE_SALT',       '6}K%C /*z*36g.Zj=bP Dm=WJ5DTlg)JicZ$:=vsw@<!#0KZV2>@)1}avxgeG%n0' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
