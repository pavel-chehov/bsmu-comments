<?php
require_once 'funcLib.php';
$networkPrefix = null;
$cookieArray=array(array('up_key_vk', 'vk', 'http://vk.com/id'), array('up_key_fb', 'fb', 'http://www.facebook.com/app_scoped_user_id/'),
array('up_key_gp', 'gp', 'https://plus.google.com/u/0/'), array('up_key_ok', 'ok', 'http://ok.ru/profile/'));

foreach ($cookieArray as $cookie){
	if(isset($_COOKIE[$cookie[0]])){
		$networkPrefix=$cookie[1];
		$networkPrefixUrl=$cookie[2];
	}
}
if ( $networkPrefix != null ) {
	$userInfo = getUserByHash( $_COOKIE[ 'up_key_' . $networkPrefix ] );
	if ( $userInfo ) {
		$userInfo['user_link'] = $networkPrefixUrl . $userInfo['network_url'];
		echo json_encode( $userInfo );
	}
} else {
	echo "null";
}
?>