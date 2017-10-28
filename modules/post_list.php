<?php
function post_list($post){
	if(!isset($post['limit'])){
		throw_403("NO_REQUIRED_INFORMATION");
		die();
	}
	global $mysql, $post_limitation, $post_preview_limitation;
	
	//防注入（辣鸡MySQL，LIMIT用不了prepere我也很绝望啊！！）
	if(!is_numeric($post['limit'])){
		throw_403("你想干嘛");
		die();
	}
	
	//即使是让客户端提交限制也是得限制一下的w
	if($post['limit'] > $post_limitation){
		$post['limit'] = $post_limitation;
	}
	
	$data = $mysql->query("SELECT * FROM post ORDER BY last_reply DESC LIMIT ".$post['limit'])->fetchAll(PDO::FETCH_ASSOC);
	foreach($data as &$i){
		$i['pid'] = (int)$i['pid'];
		$i['user_id'] = (int)$i['user_id'];
		$i['user_name'] = $mysql->query("SELECT name FROM users WHERE user_id = ?", [$i['user_id']])->fetchColumn();
		if(strlen($i['content']) > $post_preview_limitation){
			$i['summary'] = substr($i['content'], 0, $post_preview_limitation)."...";
		}else{
			$i['summary'] = $i['content'];
		}
		unset($i['content']);
	}
	
	$result = [
		"success" => true,
		"posts"   => $data
	];
	return $result;
}
?>