<?php
session_start();
require_once 'funcLib.php';
require_once 'app_config.php';

function loginUser() {
	$life_time = time() + ( 60 * 60 * 24 * 7 );
	global $cookieArray;
	$cookie = null;
	foreach ( $cookieArray as $c ) {
		if ( $_COOKIE[ $c ] ) {
			$cookie = $c;
		}
	}
	$userInfo = getUserByHash( $_COOKIE[ $cookie ] );
	if ( $userInfo['ban_time']==0 ) {
		$userInfo['identity'] = $userInfo['network_url'];
		addCommentFromPage( $userInfo );//добавляем коммент
		//обновляем куку
		setcookie( $cookie, $userInfo['user_hash'],
			$life_time,
			ACCESS_PATH,
			ACCESS_DOMAIN );
	} else {
		$ban_time= date("H:i  d.m.Y" , $userInfo["ban_time"]);
		echo "<p><h1><span style='color:red'>Вы забанены до $ban_time</h1></span></p>";
	}
}

loginUser();
//получаем данные по пользователе от вконтакта

$comments = getCommentsFromPage();
foreach ( $comments as $comment ) {
	echo $comment;
}
?>