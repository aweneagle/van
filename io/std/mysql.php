<?php

	class StdMysql implements IBlock, ICtrl{
		private $host ;
		private $port ;
		private $user ;
		private $passwd;

		public function __get($name){
			switch ($name) {
			case "host":
			case "port":
			case "user":
			case "passwd":
				return $this->$name ;
			default:
				throw new Exception("unknown property of ".get_class($this).", property=".$name);
			}
		}
		public function __set($name, $val){
			switch ($name) {
			case "host":
			case "port":
			case "user":
			case "passwd":
				return $this->$name = $val;
			default:
				throw new Exception("unknown property of ".get_class($this).", property=".$name);
			}
		}
		public function query($query, $params){
		}
	}
