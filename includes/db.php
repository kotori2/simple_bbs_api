<?php
require(dirname(__FILE__).'/../config/database.php');
class myPDO extends PDO {
	public function __construct() {
		$this->lastQuery = '';
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
	}
	public function exec($sql) {
		$this->lastQuery = $sql;
		$ret = parent::exec($sql);
		if($ret === false) {
			trigger_error('Query failed');
		} else {
			return $ret;
		}
	}
	public function query($sql, $parameters=false) {
		$this->lastQuery = $sql;
		if ($parameters == false) {
			$result = true;
			$ret = parent::query($sql);
		} else {
			$ret = parent::prepare($sql);
			if (!$ret) {
				trigger_error('Prepared query failed');
			}
			$result = $ret->execute($parameters);
		}
		if ($result === false) {
			trigger_error('Prepared query failed');
		} elseif($ret === false) {
			trigger_error('Query failed');
		} else {
			return $ret;
		}
	}
	public function prepare($sql, $options = []) {
		$this->lastQuery = $sql;
		return parent::prepare($sql, $options);
	}
	public function errorInfo() {
		$info = parent::errorInfo();
		return $this->lastQuery."\n".$info[2];
	}
};

try{
	$mysql=new myPDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);
}catch(PDOException $e){
	if (strpos($_SERVER['PHP_SELF'], 'main.php') !== false) {
		header("Maintenance: 1");
	} else {
		echo '<h1>无法连接数据库</h1>';
	}
	die();
}

$mysql->query('SET names utf8');
$mysql->query('SET time_zone = "+8:00"');
?>