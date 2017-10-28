<?php
function login($post){
	if(!isset($post['name']) || !isset($post['password'])){
		throw_403("NO_REQUIRED_INFORMATION");
		die();
	}
	global $mysql, $salt, $session_expire;
	$uid = $mysql->query("SELECT user_id FROM users WHERE name = ? AND password = ?", [$post['name'], hash_hmac("sha1", $post['password'], $salt)])->fetchColumn();
	if($uid === false){
		$result = [
			"success" => false,
			"message" => "用户名或密码错误！"
		];
	}else{
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
			"session" => $session,
			"expire"  => time() + $session_expire,
		];
	}
	return $result;
}
?>