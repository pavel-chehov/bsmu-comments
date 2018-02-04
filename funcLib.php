<?php
require_once 'connectDB.php';
define( 'HASH_PREFIX', 'bsmu_' );
function searchArticle( $page_adress ) {
	$query = "SELECT id FROM news WHERE link = '{$page_adress}';";
	$result = mysql_query( $query )
	or die( "<p>Невозможно получить адрес страницы: " . mysql_error()
	        . "</p>" );
	$row        = mysql_fetch_array( $result );
	$article_id = $row['id'];

	return $article_id;
}

function searchArticleById( $news_id ) {
	$query = "SELECT * FROM news WHERE id = '{$news_id}';";
	$result = mysql_query( $query )
	or die( "<p>Невозможно получить адрес страницы: " . mysql_error()
	        . "</p>" );
	$row = mysql_fetch_array( $result );

	return $row;
}

function searchUser(
	$user_network_url
) {//ищем юзера по url возвращаем в качестве результата всю строку row
	$query
		= "SELECT * FROM users WHERE network_url = '{$user_network_url}';";//ищем есть ли такой же url в базе
	$res = mysql_query( $query )
	or die( "<p>Невозможно сделать запрос поиска пользователя: " . mysql_error()
	        . "</p>" );
	$row = mysql_fetch_array( $res );//получение результата запроса из базы;
	return $row['user_id'];
}

function searchUserById( $user_id ) {
	$query
		= "SELECT * FROM users WHERE user_id = '{$user_id}'";//ищем есть ли такой же url в базе
	$res = mysql_query( $query )
	or die( "<p>Невозможно сделать запрос поиска пользователя: " . mysql_error()
	        . "</p>" );
	$row = mysql_fetch_array( $res );//получение результата запроса из базы;
	return $row;
}

function getUserByHash( $hash ) {
	$query
		= "SELECT first_name, last_name, image, network_url, user_hash, ban_time FROM users WHERE user_hash = '{$hash}'";//ищем есть ли такой же url в базе
	$res = mysql_query( $query )
	or die( "<p>Невозможно сделать запрос поиска пользователя: " . mysql_error()
	        . "</p>" );
	$row = mysql_fetch_array( $res );//получение результата запроса из базы;
	return $row;
}

function addUser( $userName) {//добавление пользователя
	if ( isset( $userName['first_name'], $userName['last_name'], $userName['identity'] ) ) {
		$hash_str = sha1( HASH_PREFIX . $userName['identity'] );
		$query
		          = "INSERT INTO users (first_name, last_name, image, network, network_url,user_hash, user_ip)
			VALUES ('{$userName['first_name']}', '{$userName['last_name']}','{$userName['image']}', 
				'{$userName['network']}', '{$userName['identity']}', '{$hash_str}');";
		$result = mysql_query( $query )
		or die( "<p>Невозможно добавить пользователя " . mysql_error()
		        . "</p>" );
	}
}

function updateUser( $userName, $user_id ) {
	$query
		    = "UPDATE users SET first_name='{$userName['first_name']}',last_name='{$userName['last_name']}', image='{$userName['image']}' WHERE user_id='{$user_id}';";
	$result = mysql_query( $query );
}

function addComment( $article_id, $user_id, $comment ) {//добавляем комментарий
	$query    = "SELECT ban_time FROM users WHERE user_id='{$user_id}';";
	$res      = mysql_query( $query );
	$ban_time = mysql_fetch_row( $res );
	$ip = $_SERVER["REMOTE_ADDR"];
	if ( $ban_time[0] != 0 ) {
		return false;
	}
	$query = "INSERT INTO comments (news_id, user_id, comment, add_time, ip)
	          VALUES ('{$article_id}', '{$user_id}', '{$comment}', NOW(), '{$ip}');";
	$res = mysql_query( $query )
	or die( "<p>Невозможно сделать запись комментария: " . mysql_error()
	        . "</p>" );
	if ( $res ) {
		return true;
	} else {
		return false;
	}
}

///Функция выхвода комментариев на страничку, вместо page_adress можно вставить айдишник новости, немного изменив запрос к базе
function getComments( $page_adress ) {
	//INET_ATON-преобразует ip В число и INET_NTOA-число в ip
	$newsID     = searchArticle( $page_adress );
	//Создаем запрос на слияние данных о пользователях с данными об их комментариях
	$query
		= "SELECT id, user_id, comment, DATE_FORMAT(add_time,'%H:%i %d.%m.%Y') add_time, first_name, last_name, image, network_url, network,ban_time, user_ip
		FROM users NATURAL JOIN comments WHERE news_id='{$newsID}' AND deleted=false ORDER BY id;";
	$result_obj = mysql_query( $query )
	or die( "<p>Невозможно получить данные о комментариях: " . mysql_error()
	        . "</p>" );
	$commentArray = array();
	while ( $row
		= mysql_fetch_array( $result_obj ) ) { //Сюда должна лечь новая строка ассоциативного массива
		if ( $row != null || $row != false ) {
			array_push( $commentArray, $row );
		}
	}

	return $commentArray;
}

//получение списка существующих пользователей
function getUsers() {
	$query = "SELECT * FROM users ORDER BY last_name";
	$result = mysql_query( $query )
	or die( "<p>Невозможно получить данные о пользователях: " . mysql_error()
	        . "</p>" );
	$usersArray = array();
	while ( $row = mysql_fetch_array( $result ) ) {
		array_push( $usersArray, $row );
	}

	return $usersArray;
}
function addBannedComment($comment_id){
	$query = "UPDATE comments SET banned=1 WHERE id='{$comment_id}';";
	$result = mysql_query( $query )
	or die( "<p>Невозможно установить флаг бана для комментария " . mysql_error()
	        . "</p>" );
}
//бан пользователя
function banUser( $user_id, $ban_time ) {
	if ( $ban_time ) {
		switch ( $ban_time ) {
			case 'day':
				$ban_time = time() + 24 * 3600;
				break;
			case 'week':
				$ban_time = time() + 7 * 24 * 3600;
				break;
			case 'month':
				$ban_time = time() + 31 * 24 * 3600;
				break;
			case 'year':
				$ban_time = time() + 12 * 31 * 24 * 3600;
				break;
			case 'forever':
				$ban_time = - 1;
				break;
			default:
				break;
		}
	}
	$query
		= "UPDATE users SET ban_time='{$ban_time}' WHERE user_id='{$user_id}';";
	$res = mysql_query( $query ) or die( "<p>Невозможно забанить пользователя: "
	                                     . mysql_error() . "</p>" );
}

//получение списка забаненых пользователей
function getBannedUsers() {
	$query
		= "SELECT *  FROM users WHERE ban_time!=0;";
	$result = mysql_query( $query )
	or die( "<p>Невозможно получить данные о заблокированных пользователях: " . mysql_error()
	        . "</p>" );
	$usersArray = array();
	while ( $row = mysql_fetch_array( $result ) ) {
		array_push( $usersArray, $row );
	}

	return $usersArray;
}
function getLastBannedCommentForUser($user_id){
	$query = "SELECT * FROM comments WHERE user_id='{$user_id}' AND banned=1;";
	$res=mysql_query($query)
	or die( "<p>Невозможно получить массив забаненных комментариев: " . mysql_error()
	        . "</p>" );
	$bannedComments = array();
	while ( $row = mysql_fetch_array( $res ) ) {
		array_push( $bannedComments, $row );
	}
	return $bannedComments[sizeof($bannedComments)-1];
}
//получение списка существующих статей
function getArticles() {
	$query = "SELECT * FROM news ORDER BY date;";
	$res = mysql_query( $query )
	or die( "<p>Невозможно получить список новостей: " . mysql_error()
	        . "</p>" );
	$articles_array = array();
	while ( $row = mysql_fetch_array( $res ) ) {
		array_push( $articles_array, $row );
	}

	return $articles_array;
}

//удяление комментария
function deleteComment( $comment_id ) {
	$query = "UPDATE comments SET deleted=1 WHERE id='{$comment_id}';";
	$res = mysql_query( $query )
	or die( "<p>Невозможно удалить комментарий: " . mysql_error()
	        . "</p>" );
}
//проверка данных на корректность
function CompareString($comment){
	$comment=trim($comment);
	$comment = strip_tags($comment);
	$comment=htmlspecialchars($comment);
	$comment= mysql_real_escape_string($comment);

	if($comment) return $comment;
}
/*добавляем коммментарий на существующую страницу , для этого мы запихиваем в пост
массив все данные о том кто и что анписал, после чего функция забирает данныне из пост массива и доабвляет в базу*/
function addCommentFromPage( $userName ) {
	if ( isset( $_POST['pageUrl'] ) ) {
		$page_url = $_POST['pageUrl'];
	} else {
		$page_url = $_SESSION['page_url'];
	}

	if ( isset( $_POST['currentComment'] ) ) {
		$comment=CompareString( $_POST['currentComment']);
		if ( isset( $userName ) && $comment) {
			$article_id
				= searchArticle( $page_url ); //Получаем идентификатор страницы на которой нужно разместить комментарий
			$user_id
				= searchUser( $userName['identity'] );//первоначально ищем пользователя
			if ( $user_id ) {//Пишем коммент
				updateUser( $userName, $user_id);
			} else {//если юзера нет- добавляем и пишем коммент
				addUser( $userName);
				$user_id = searchUser( $userName['identity'] );
			}
			if ( $comment != "" ) {
				addComment( $article_id, $user_id, $comment );
			}
		}
	}
}

//получаем текущие существующие комментарии со страницы
function getCommentsFromPage() {
	if ( isset( $_POST['pageUrl'] ) ) {
		$commentOut = getComments( $_POST['pageUrl'] );
	} //Получаем комментарии
	else {
		$commentOut = getComments( $_SESSION['page_url'] );
	}
	$html_text = array();
	if ( is_array( $commentOut ) && sizeof( $commentOut ) > 0 ) {
		$commentOut = array_reverse( $commentOut, true );
		foreach ( $commentOut as $comment ) {
			switch ( $comment['network'] ) {
				case 'vk.com':
					$networkPrefix = 'http://vk.com/id';
					break;
				case 'facebook.com':
					$networkPrefix = 'http://www.facebook.com/';
					break;
				case 'plus.google.com':
					$networkPrefix = 'https://plus.google.com/u/0/';
					break;
				case 'ok.ru':
					$networkPrefix = 'https://ok.ru/profile/';
					break;
			}
			$text = "<div class='comment'>" .
			        /*вывод аватарки
			"<a href=\"http://vk.com/id".$comment['network_url']."\">".
			"<img src=\"".$comment['image']."\"/></a>".*/
			        "<span> <h4>" . "<a href=" . $networkPrefix
			        . $comment['network_url'] . ">" .
			        $comment['first_name'] . " " . $comment['last_name']
			        . "</a> "
			        . $comment['add_time'] . "</h4><p>" . $comment['comment']
			        . "</p></span>" .
			        "</div>
			        <div class='devinder'></div>";
			array_push( $html_text, $text );
		}
	} else {
		$text
			= "<div class='comment'>
				<span><p> Пока нет комментариев...</p></span>
			  </div>";
		array_push( $html_text, $text );
	}

	return $html_text;
}

//получаем хэш пользователя по его айдишнику в соцсети
function getHashForUser( $network_url ) {

	$query
		= "SELECT user_hash FROM users WHERE network_url = '{$network_url}'";//ищем есть ли такой же url в базе
	$res = mysql_query( $query )
	or die( "<p>Невозможно сделать запрос поиска пользователя: " . mysql_error()
	        . "</p>" );
	$row = mysql_fetch_array( $res );//получение результата запроса из базы;
	return $row['user_hash'];
}

///функция получает готовый массив с данными из соц сетки, проверяет ест ли пользователь из массива
//в базе, и если есть то обновляет о нем инфу, естли нет - то добавляет нового пользователя
//и после чего устанавливает куку
function setUserCookie( $userName, $cookieName ) {

	$user_id = searchUser( $userName['identity'] );
	( $user_id )
		? updateUser( $userName, $user_id, $_SERVER["REMOTE_ADDR"] )
		:
		addUser( $userName, $_SERVER["REMOTE_ADDR"] );

	$str       = getHashForUser( $userName['identity'] );
	$life_time = time() + ( 60 * 60 * 24 * 7 );
	setcookie( $cookieName, $str, $life_time, ACCESS_PATH,
		ACCESS_DOMAIN );
	deleteOtherCookie( $cookieName, $str );
}

//функция удаляет все ненужные куки(в один момент времени может бытьу станвлена только одна кука)
function deleteOtherCookie( $cookieName, $str ) {
	$life_time      = - 3600;
	$cookie_postfix = array( 'vk', 'fb, gp, ok' );
	foreach ( $cookie_postfix as $postfix ) {
		if ( isset( $_COOKIE[ 'up_key_' . $postfix ] )
		     && $cookieName != 'up_key_' . $postfix
		) {
			setcookie( $cookieName, $str, $life_time, ACCESS_PATH,
				ACCESS_DOMAIN );
		}
	}
}

?>