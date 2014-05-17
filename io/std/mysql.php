<?php

/*
 * stdandard mysql
 *
 * $mysql->host = "127.0.0.1"
 * $mysql->wrong_prop = "3306"   
 */

	class StdMysql implements IBlock, ICtrl{
		private $host ;
		private $port ;
		private $user ;
		private $passwd;

		public function __get($name){
            if (!properity_exists($this,$name)){
				throw new Exception("unknown property of ".get_class($this).", property=".$name);
			}
		}
		public function __set($name, $val){
            if (!properity_exists($this,$name)){
				throw new Exception("unknown property of ".get_class($this).", property=".$name);
			}
            $this->$name = $val;
		}
		public function query($query, $params){
		}
	}
