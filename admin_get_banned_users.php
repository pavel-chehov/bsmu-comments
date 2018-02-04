<?php
require_once 'funcLib.php';
require_once 'funcLib.php';
session_start();
header( 'Content-type: text/html; charset=utf-8' );
$bannedUsers = getBannedUsers();

if ( isset( $_REQUEST['unban_user'] ) ) {
	banUser( $_REQUEST['unban_user'], null );
}

if ( sizeof( $bannedUsers ) == 0 ) {
	echo "Нет забаненных пользователей";
	return;
}
echo "<div class='devinder'></div><br />";
foreach ( $bannedUsers as $_user ) {
	$bannedComment=getLastBannedCommentForUser($_user["user_id"]);
	$article=searchArticleById($bannedComment['news_id']);
	echo "<div class='comment'>";
	echo $_user['first_name'] . " " . $_user['last_name'] . " - id: "
	     . $_user['user_id'] . "</br>";
	if ( $_user['ban_time'] != 0 ) {
		echo "Бан истекает: " . date( "H:i d.m.Y", $_user['ban_time'] )."</br>";
		echo "Причина: " . $bannedComment['add_time']."</br>";
		echo  $bannedComment['comment']."</br>";
		echo "<a class='enter_btn' href='".$article['link']."'><i class='fa fa-arrow-left fa-lg'></i>".$article['title']."</a>";
		echo
		"<div class='clearfix'></div><button class='green_btn' type='submit' name='unban_user' onclick=\"banPressed('null','"
			. $_user['user_id']
			. "','unban_user', '../admin_get_banned_users.php')\"/><i class='fa fa-legal fa-lg'></i> Разбанить пользователя</button></br>";
		echo "</div><div class='devinder'></div><br />";
	}
}

?>