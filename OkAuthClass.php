<?php
class Utils {
	public static function redirect( $uri = '' ) {
		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: " . $uri, true, 302 );
		exit;
	}
}
class OAuthOK {

	const APP_ID = 1146120960; //ID приложения
	const APP_PUBLIC = 'CBAMGBFFEBABABABA'; //Публичный ключ
	const APP_SECRET = '02512D53BCA1B025A431A034'; //Защищенный ключ
	const URL_CALLBACK = 'http://comments.akson.by/setOkCookie.php'; //URL, на который произойдет перенаправление после авторизации
	const URL_AUTHORIZE = 'http://www.odnoklassniki.ru/oauth/authorize';
	const URL_GET_TOKEN = 'http://api.odnoklassniki.ru/oauth/token.do';
	const URL_ACCESS_TOKEN = 'http://api.odnoklassniki.ru/fb.do';

	private static $token;
	public static $userId;
	public static $userData;

	/**
	 * @url http://apiok.ru/wiki/pages/viewpage.action?pageId=81822109
	 */
	public static function goToAuth() {
		Utils::redirect( self::URL_AUTHORIZE .
		                 '?client_id=' . self::APP_ID .
		                 '&response_type=code' .
		                 '&redirect_uri=' . urlencode( self::URL_CALLBACK ) );
	}

	public static function getToken( $code ) {

		$data = array(
			'code'          => trim( $code ),
			'redirect_uri'  => self::URL_CALLBACK,
			'client_id'     => self::APP_ID,
			'client_secret' => self::APP_SECRET,
			'grant_type'    => 'authorization_code'
		);

		$opts = array(
			'http' =>
				array(
					'method'  => 'POST',
					'header'  =>
						"Content-type: application/x-www-form-urlencoded\r\n" .
						"Accept: */*\r\n",
					'content' => http_build_query( $data )
				)
		);

		if ( ! ( $response = @file_get_contents( self::URL_GET_TOKEN, false,
			stream_context_create( $opts ) ) )
		) {
			return false;
		}

		$result = json_decode( $response );
		if ( empty( $result->access_token ) ) {
			return false;
		}

		self::$token = $result->access_token;

		return true;
	}

	/**
	 * Если данных недостаточно, то посмотрите что можно ещё запросить по этой ссылке
	 * @url http://apiok.ru/wiki/display/api/users.getCurrentUser+ru
	 */
	public static function getUser() {

		if ( ! self::$token ) {
			return false;
		}

		$url = self::URL_ACCESS_TOKEN .
		       '?access_token=' . self::$token .
		       '&method=users.getCurrentUser' .
		       '&application_key=' . self::APP_PUBLIC .
		       '&sig=' . md5( 'application_key=' . self::APP_PUBLIC
		                      . 'method=users.getCurrentUser'
		                      . md5( self::$token . self::APP_SECRET ) );

		if ( ! ( $response = @file_get_contents( $url ) ) ) {
			return false;
		}

		$user = json_decode( $response );

		if ( empty( $user ) ) {
			return false;
		}

		self::$userId = $user->uid;

		return self::$userData = $user;
	}
}
?>
