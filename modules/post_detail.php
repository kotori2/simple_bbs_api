<?php
function post_detail($post){
	if(!isset($post['pid']) || !isset($post['page'])){
		throw_403("NO_REQUIRED_INFORMATION");
		die();
	}
	global $uid, $mysql, $limit_per_page;
	
	if(!is_numeric($post['page']) || $post['page'] < 0){
		throw_403("你想干嘛");
		die();
	}
	
	$lower_limit = $post['page'] * 20;
	$upper_limit = ($post['page'] + 1) * 20;
	
	$post_data = $mysql->query("SELECT * FROM post WHERE pid = ?", [$post['pid']])->fetch(PDO::FETCH_ASSOC);
	$post_data['pid'] = (int)$post_data['pid'];
	$post_data['user_id'] = (int)$post_data['user_id'];
	$post_data['user_name'] = $mysql->query("SELECT name FROM users WHERE user_id = ?", [$post_data['user_id']])->fetchColumn();
	
	$data = $mysql->query("SELECT * FROM reply AS b 
		WHERE rid NOT IN (
			SELECT * FROM(
				SELECT rid FROM reply AS a 
				WHERE pid = ? 
				ORDER BY rid 
				ASC LIMIT ".$lower_limit.") AS c
			)
		AND pid = ?
		ORDER BY rid 
		ASC LIMIT 20", [$post['pid'], $post['pid']])->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($data as &$i){
		unset($i['pid']);
		$i['rid'] = (int)$i['rid'];
		$i['user_id'] = (int)$i['user_id'];
		$i['user_name'] = $mysql->query("SELECT name FROM users WHERE user_id = ?", [$i['user_id']])->fetchColumn();
		if($i['to_rid'] === null){
			$i['to_rid'] = null;
		}else{
			$i['to_rid'] = (int)$i['to_rid'];
		}
	}
	
	$result = [
		"success" => true,
		"post"    => $post_data,
		"replies" => $data
	];
	return $result;
}
?>