<?php
require_once 'funcLib.php';
require_once 'OkAuthClass.php';

if ( ! empty( $_GET['error'] ) ) {
	// Пришёл ответ с ошибкой. Например, юзер отменил авторизацию.
	echo "null";
} elseif ( empty( $_GET['code'] ) ) {
	// Самый первый запрос
	OAuthOK::goToAuth();
} else {
	// Пришёл ответ без ошибок после запроса авторизации
	if ( ! OAuthOK::getToken( $_GET['code'] ) ) {
		die( 'Error - no token by code' );
	}
	$user = OAuthOK::getUser();

	$userInfo['first_name'] = $user->first_name;
	$userInfo['last_name']  = $user->last_name;
	$userInfo['identity'] = $user->uid;
	$userInfo['network']  = 'ok.ru';
	$userInfo['image'] = $user->pic_1;

	if(sizeof($userInfo)>0) setUserCookie( $userInfo, 'up_key_ok' );
}

echo "<script type='text/javascript'>window.close();</script>";

?>