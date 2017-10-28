<?php
function post($post){
	if(!isset($post['title']) || !isset($post['content'])){
		throw_403("NO_REQUIRED_INFORMATION");
		die();
	}
	global $uid, $mysql, $title_length, $post_length;
	if(strlen($post['title']) > $title_length){
		$result = [
			"success" => false,
			"message" => "标题太长辣！"
		];
		return $result;
	}
	if(strlen($post['content']) > $title_length){
		$result = [
			"success" => false,
			"message" => "帖子内容太长辣！"
		];
		return $result;
	}
	$mysql->query("INSERT INTO `post` (user_id, title, content) VALUES(?, ?, ?)", [$uid, $post['title'], $post['content']]);
	$pid = $mysql->query("SELECT LAST_INSERT_ID()")->fetchColumn();
	$result = [
		"success" => true,
		"pid"     => (int)$pid
	];
	return $result;
}
?>