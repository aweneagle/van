<?php


	/* csv modules */
	class CsvMysql implements ICsv {
		public $key = "id";
		public $fields = array(
			"id" => "int(20)",
			"name" => "str(20)",
			"data" => "text"
		);
		public $table ;
		public $db ;
		public $host;
		public $port;
		public $user;
		public $passwd;
		private function prepare($csv, &$sql, &$params){
		}
		private function db(){
			$mysql = new StdMysql();
			$mysql->host = $this->host;
			$mysql->port = $this->port;
			$mysql->user = $this->user;
			$mysql->passwd = $this->passwd;
			$mysql->options['FETCH_STYLE'] = FETCH_ARR;
			return $mysql;
		}
		public function fetch(array $csv){
			$this->prepare($csv, "SELECT", $sql, $params);
			$res = $this->db()->query($sql, $params);
			return $res;
		}
		public function replace(array $csv){
			$this->prepare($csv, "REPLACE", $sql, $params);
			$res = $this->db()->query($sql, $params);
			return $res;
		}
		public function delete(array $csv){
			$this->prepare($csv, "DELETE", $sql, $params);
			$res = $this->db()->query($sql, $params);
			return $res;
		}
	}
