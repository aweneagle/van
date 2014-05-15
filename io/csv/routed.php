<?php


	class CsvRoutedDb implements ICsv{
		private $key = 'id';
		private $fields = array(
			'id' => 'int(20)',
			'name' => 'str(20)',
			'data' => 'text',
		);
		private $db = array( new CsvMysql, new CsvMysql, new CsvMysql );
		private $route = 'hex_2';
		private function get_router_func(){
			switch ($this->route) {
			case 'hex_2':
				return function($id){ return sprintf("%x",2,$id); }
			default:
				throw new Exception("unknown router, router=".$this->route);
			}
		}
		private function db($csv){
			$this->prepare($csv, $key, $val);
			$router = $this->get_router_func();
			$db_no = $router($key);
			if (!isset($this->db[$db_no])) {
				throw new Exception("outside the router range, no db found for key=".$key);
			}
			return $this->db[$db_no];
		}
		public function fetch(array $csv){
			$this->db($csv)->fetch($csv);
		}
		public function replace(array $csv){
			$this->db($csv)->replace($csv);
		}
		public function delete(array $csv){
			$this->db($csv)->delete($csv);
		}
	}
