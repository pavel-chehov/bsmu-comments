<?php
require_once 'funcLib.php';

//require_once realpath(dirname(__FILE__) . '/google_php_sdk/autoload.php');

function loginGP() {
	$accessToken = $_POST["access_token"];
	//Проверка действительности токена
	$tokenInfo
		= json_decode( file_get_contents( 'https://www.googleapis.com/oauth2/v1/tokeninfo?'
		                                  . 'access_token=' . $accessToken ),
		true );
	if ( $tokenInfo[0] == 'invalid_token' ) {
		return null;
	} else {
		//Запрашиваем данные пользователя
		$userInfoFromServer
			= json_decode( file_get_contents( 'https://www.googleapis.com/plus/v1/people/me?'
			                                  . 'access_token='
			                                  . $accessToken ), true );
		$userInfo = array();
		//Записываем массив в совместимый формат для дальнейшей обработки
		if ( isset( $userInfoFromServer ) ) {
			foreach ( $userInfoFromServer as $key => $value ) {
				switch ( $key ) {
					case 'name':
						$userInfo['first_name'] = $value['givenName'];
						$userInfo['last_name']  = $value['familyName'];
						break;
					case 'image':
						$userInfo['image'] = $value['url'];
						break;
					case 'id':
						$userInfo['identity'] = $value;
						break;
					default:
						break;
				}
			}
			$userInfo['network'] = "plus.google.com";

			return $userInfo;
		} else {
			return null;
		}
	}

}

$userInfo = loginGP();
if(sizeof($userInfo)>0) setUserCookie($userInfo, 'up_key_gp');
?>