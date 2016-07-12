<?php
# Database Configuration
define( 'DB_NAME', 'wp_disputebils16' );
define( 'DB_USER', 'disputebils16' );
define( 'DB_PASSWORD', 'Qr0x8A0pfyyyhtZVr1gQ' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wpt6_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         '+g:?sL;xK4Os|ybO1)z4P}yF+$|TB||;Kfr+s<c+w#f6PBJf7mjg+4Z}qOlL`VZd');
define('SECURE_AUTH_KEY',  'MuRJ~n$Z~1<s0Z^c:^39M@u~^<t)B}v4&<=5s %u{O/H%^kIk&ipw#ZShTTCAs6-');
define('LOGGED_IN_KEY',    '^<[eRENY&B>4S7Y75uYO]0ssGC[`6C[UpU-nBqKJ!D7s8]jg[H:k,rz^$.tWwO Y');
define('NONCE_KEY',        'h%wC@QdX&uzV:UBnAy|:2-HbSLp41rkc9TTdAR?|I{9XW+F)i$Yl-.]5hMzUT6#R');
define('AUTH_SALT',        '.BgW4+(+|yMpaZ-0&l?d7,9YtL+hxbeu%#P$`tuE<_+m=ww1nBHb+6~es2nA_0bY');
define('SECURE_AUTH_SALT', 'v6?T$#yvSEZ$sGpE/83f9*R6BR]Un+I6aR=Z$?bh>)*74jBQhS16>g/q2gvLBF:-');
define('LOGGED_IN_SALT',   'v5=N:Of{o-aNKFqG&+Xyui,:+@9SYa-K#ic%09|p2_TjlNCdU9b%7Zh+oO)4e6jf');
define('NONCE_SALT',       'Amav?6;qE.g`nA!y+l-3,<4GZmM_sI|Q=}+>@s{-E6PpBi;[O:za|)5^*XYpHu/(');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'disputebils16' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', 'a9e0b63471d404e62e06f977ef359cb51bf699df' );

define( 'WPE_FOOTER_HTML', "" );

define( 'WPE_CLUSTER_ID', '32240' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '162.242.254.170' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'disputebils16.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-32240', );

$wpe_special_ips=array ( 0 => '162.242.254.170', );

$wpe_ec_servers=array ( );

$wpe_largefs=array ( );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );


# WP Engine ID


# WP Engine Settings






define('WP_DEBUG', false);
define('WP_MEMORY_LIMIT', '128M');

# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');

$_wpe_preamble_path = null; if(false){}
//Disable File Edits
define('DISALLOW_FILE_EDIT', true);