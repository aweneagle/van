<?php

/*
 * stdandard mysql
 *
 */

class StdMysql implements IBlock, ICtrl{
	private $host ;
	private $port ;
	private $user ;
	private $passwd;
	private $options;

	public function __get($name){
		if (!properity_exists($this,$name)){
			php_error("unknown property of ".get_class($this).", property=".$name);
			return false;
		}
		return $this->$name;
	}

	public function __set($name, $val){
		if (!properity_exists($this,$name)){
			php_error("unknown property of ".get_class($this).", property=".$name);
			return false;
		}
		$this->$name = $val;
		return true;
	}

	public function query($query, $params){
	}
}
