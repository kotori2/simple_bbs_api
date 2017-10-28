<?php
function reply($post){
	if(!isset($post['pid']) || !isset($post['content']) || !isset($post['to_rid'])){
		throw_403("NO_REQUIRED_INFORMATION");
		die();
	}
	
	global $uid, $mysql, $post_length;
	
	if($post['to_rid'] === false){
		$post['to_rid'] = null;
	}
	//检查帖子是否存在
	$pid = $mysql->query("SELECT pid FROM post WHERE pid = ?", [$post['pid']])->fetchColumn();
	if($pid === false){
		$result = [
			"success" => false,
			"message" => "帖子不存在！"
		];
		return $result;
	}
	
	//检查回复是否过长
	if(strlen($post['content']) > $post_length){
		$result = [
			"success" => false,
			"message" => "回复太长辣！"
		];
		return $result;
	}
	
	$previous_rid = (int)$mysql->query("SELECT rid FROM reply WHERE pid = ? ORDER BY rid DESC LIMIT 1", [$pid])->fetchColumn(); //其实当没有回复的时候也没问题，查询结果是false，转换成int是0
	$rid = $previous_rid + 1;
	
	$mysql->query("INSERT INTO reply (pid, rid, user_id, content, to_rid) VALUES(?,?,?,?,?)", [$post['pid'], $rid, $uid, $post['content'], $post['to_rid']]);
	
	$result = [
		"success" => true,
		"rid"     => $rid
	];
	return $result;
}
?>