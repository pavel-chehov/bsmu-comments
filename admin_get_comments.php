<?php
require_once 'funcLib.php';
session_start();
header( 'Content-type: text/html; charset=utf-8' );
$articles = getArticles();
if ( $_REQUEST ) {

	if ( ! isset( $_REQUEST['article'] ) && isset( $_SESSION['article'] ) ) {
		$_REQUEST['article'] = $_SESSION['article'];
	}
	if ( isset( $_REQUEST['user_id'] ) && isset( $_REQUEST['ban'] ) ) {
		banUser( $_REQUEST['user_id'], $_REQUEST['ban'] );
		addBannedComment($_REQUEST['comment_id']);
	}
	if ( isset( $_REQUEST['unban_user'] ) ) {
		banUser( $_REQUEST['unban_user'], null );
	}
	if ( isset( $_REQUEST['delete'] ) ) {
		deleteComment( $_REQUEST['delete'] );
	}

	if ( $_REQUEST['article'] ) {
		$_SESSION['article'] = $_REQUEST['article'];
		$article             = searchArticleById( $_REQUEST['article'] );
		$comments            = getComments( $article['link'] );
		echo "<button class='green_btn' type='submit' name='back' 
									onclick=\"location.href=location.href\" /><i class='fa fa-arrow-left fa-lg'></i> Назад к списку статей</button>";
									echo "<br /><u><h3><span>Комментарии к статье:</span></h3></u>";
		if ( sizeof( $comments ) != null) {
			$comments = array_reverse( $comments, true );
			foreach ( $comments as $comment ) {
				//Начало вывода комментария
				echo "<div class='comment'>" .
				"<div class='mainInfo'>";
				echo "<h3>".$comment['first_name'] . " " . $comment['last_name'] . "</h3> - <i class='fa fa-clock-o'></i> "
				     . $comment['add_time'] . "<br /><p>" .
				     $comment['comment'] . "</p></div>";
				echo "<div class='additionalInfo'>";
				echo "<div class='articleTitle'>Перейти к новости: " .
				"<a href='" . $article['link'] . "' target='_blank'>" . $article['title'] . "</a></div>";
				echo "<div class='clearfix'></div>";
				if ( ! $comment['ban_time'] ) {
					echo "<div class='ban_form' id='form_" . $comment['id'] . "'>
					<div class='gray_btn'>
					<i class='fa fa-user-times fa-lg hoverHide'></i>
					<ul class='ban_buttons'>
						<h3><i class='fa fa-user-times fa-lg'></i> Блокировка пользователя</h3><br />
									<li><input type='radio'  name='ban' value='day'/>День</li>
									<li><input type='radio'  name='ban' value='week'/>Неделя</li>
									<li><input type='radio'  name='ban' value='month'/>Месяц</li>
									<li><input type='radio'  name='ban' value='year'/>Год</li>
									<li><input type='radio'  name='ban' value='forever'/>Навсегда</li>
									<br />
									<button class='ban_btn' type='submit' name='user_id' 
									onclick=\"banPressed('" . $comment['id']
					     . "','" . $comment['user_id'] . "','ban', '../admin_get_comments.php')\" /><i class='fa fa-legal fa-lg'></i> Забанить пользователя</button>
									</ul>
						</div>
									<button class='delete_btn' type='submit' name='comment_id'
									onclick=\"banPressed('" . $comment['id']
					     . "','" . $comment['user_id'] . "','delete', '../admin_get_comments.php')\" /><i class='fa fa-times-circle-o fa-lg'></i> Удалить комментарий</button>
								
							</div><div class='clearfix'></div>
							</div>";
				} else {
					echo
						"<div class='comment'><div class='ban_form' id='form_" . $comment['id'] . "'>
							   Бан истекает " . date( "d.m.Y в H:m",
							$comment['ban_time'] ) ."</br>".
						"<button class='green_btn' type='submit' name='unban_user' onclick=\"banPressed('"
						. $comment['id'] . "','" . $comment['user_id'] . "','unban_user', '../admin_get_comments.php')\"/><i class='fa fa-legal fa-lg'></i> Разбанить пользователя</button>
							<button class='delete_btn' type='submit' name='comment_id'
									onclick=\"banPressed('" . $comment['id']
					     . "','" . $comment['user_id'] . "','delete', '../admin_get_comments.php')\" /><i class='fa fa-times-circle-o fa-lg'></i> Удалить комментарий</button>
								</div><div class='clearfix'></div>
						</div></div>";
				}
				echo "<div class='devinder'></div></div>";
			}
		} else {
			echo "<div>Пока нет комментариев</div>";
		}
	}
}

?>

