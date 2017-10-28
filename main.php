<?php
date_default_timezone_set("Asia/Shanghai");

//连接数据库
require(dirname(__FILE__).'/includes/db.php');
$mysql->query('START TRANSACTION');

require(dirname(__FILE__).'/config/config.php');
require(dirname(__FILE__).'/includes/error.php');
if(!isset($_SERVER['PATH_INFO'])){
	throw_403("NO_ACTION_FOUND");
	die();
}

//处理提交数据
$post = json_decode(file_get_contents("php://input"), true);
if($post === null){
	throw_403("INVALID_DATA");
	die();
}

//将请求的信息传递给模块
$action = explode("/", $_SERVER['PATH_INFO'])[1]; //拆分传入的PATH，防止奇奇怪怪的绕过
if(!in_array($action, ["login", "regist"])){ //检查session
	if(!isset($_SERVER['HTTP_USER_ID']) || !isset($_SERVER['HTTP_SESSION'])){
		throw_403("NO_SESSION_FOUND");
		die();
	}
	$session_check = $mysql->query("SELECT session_expire FROM users WHERE user_id = ? AND session = ?", [$_SERVER['HTTP_USER_ID'], $_SERVER['HTTP_SESSION']])->fetchColumn();
	if($session_check === false){
		throw_403("INVALID_SESSION");
		die();
	}else if(strtotime($session_check) < time()){
		throw_403("SESSION_EXPIRED!");
		die();
	}else{
		$uid = $_SERVER['HTTP_USER_ID'];
	}
}
require("config/config.php");
if((@include "modules/".$action.".php") === false){
	throw_404("MODULE_NOT FOUND");
	die();
}
$result = call_user_func($action, $post);

//返回信息
header("Content-Type: application/json");
print(json_encode($result));
$mysql->query('commit');
?>