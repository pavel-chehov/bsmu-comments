<?php
require_once 'funcLib.php';
require_once 'app_config.php';

$bannedUsers=getBannedUsers();
if(! is_array($bannedUsers)) {
	echo "Ошибка получения массива забаненых пользователей";
	exit();
}
$actualTime=time();
foreach($bannedUsers as $user){
	if($user["ban_time"]<=$actualTime){
		$query = "UPDATE users SET ban_time = 0 WHERE user_id ='{$user['user_id']}';";
		$res = mysql_query( $query ) or die( "<p>Невозможно разбанить пользователя: "
		                                     . mysql_error() . "</p>" );
	}
}
?>