<?php
require_once 'funcLib.php';
$comments=getCommentsFromPage();
echo "<h3>Последние комментарии:</h3>";
foreach ($comments as $comment) {
	echo $comment;
}
?>