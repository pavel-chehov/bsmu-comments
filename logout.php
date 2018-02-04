<?php
require_once 'app_config.php';
//Выход
if ( $_COOKIE[ 'vk_app_' . $vkAppId ] ) {
	setcookie( 'vk_app_' . $vkAppId, $_COOKIE[ 'vk_app_' . $vkAppId ],
		time() - 3600, ACCESS_PATH, ACCESS_DOMAIN );
}
if ( $_COOKIE[ 'fbsr_' . $fbAppId ] ) {
	setcookie( 'fbsr_' . $fbAppId, $_COOKIE[ 'fbsr_' . $fbAppId ],
		time() - 3600 );
}
$cookieArray = array(
	'up_key_vk',
	'up_key_fb',
	'up_key_gp',
	'up_key_ok'
);
foreach ( $cookieArray as $cookie ) {
	if ( $_COOKIE[ $cookie ] ) {
		setcookie( $cookie, $_COOKIE[ $cookie ], time() - 3600, ACCESS_PATH,
			ACCESS_DOMAIN );
	}
}
echo "logout";

?>