<?php
function regist($post){
	if(!isset($post['name']) || !isset($post['password'])){
		throw_403("NO_REQUIRED_INFORMATION");
		die();
	}
	global $mysql, $salt, $session_expire;
	//检测同名用户是否被注册过
	$check = $mysql->query("SELECT user_id FROM users WHERE name = ?", [$post['name']])->fetchColumn();
	if($check){
		$result = [
			"success" => false,
			"message" => "您已注册过名为 ".$post['name']." 的用户，ID为 ".$check
		];
		return $result;
	}
	
	$mysql->query("INSERT INTO users (name, password) VALUES(?, ?)", [$post['name'], hash_hmac("sha1", $post['password'], $salt)]);
	$uid = $mysql->query("SELECT LAST_INSERT_ID()")->fetchColumn();
	
	//生成随机session
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	mt_srand((double)microtime()*1000000*getmypid());
	$session = '';   
	while(strlen($session) < 64){
		$session .= substr($chars, (mt_rand() % strlen($chars)), 1);
	}
	
	//更新session
	$mysql->query("UPDATE users SET session = ?, session_expire = ? WHERE user_id = ?", [$session, date("Y-m-d H:i:s", time() + $session_expire), $uid]);
	
	$result = [
		"success" => true,
		"user_id" => (int)$uid,
		"name"    => $post['name'],
		"session" => $session,
		"expire"  => time() + $session_expire)
	];
	return $result;
}
?>