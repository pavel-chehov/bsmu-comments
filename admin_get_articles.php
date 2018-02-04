<?php
require_once 'funcLib.php';
$articles=getArticles();
if(is_array($articles)){
	$i=0;
	echo "<h3><span><i class='fa fa-newspaper-o fa-lg'></i> Статьи с комментариями:</span></h3>
	<div class='clearfix'></div>";
	foreach($articles as $article){
		$i++;
		echo "<div class='article'>";
		echo "<h4><i class='fa fa-lg fa-clock-o'></i> ".$article['date']."</h4><br />";
		echo "<h3>".$article['title']."</h3>";
		echo "<button class='enter_btn' type='submit' value='".$article['id'].
		"' onclick=\"insertNewData('article=".$article['id']."', '../admin_get_comments.php', 'list' ,'POST')\"/><i class='fa fa-reorder fa-lg'></i> Список комментариев</button><br />";
		echo "</div>";
		if(!($i%2)){
			echo "<div class='clearfix'></div>";
		}
	}
}
?>