<?php



	class CsvMemcached implements ICsv {
		public $key="id";
		public $fields = array(
			"id" => "int(20)",
			"name" => "str(20)",
			"data" => "text"
		);
		public $host ;
		public $port ;
		public $EXPIRED = null;
		public $COMPRESS = true;
		private function db(){
			$memcached = new StdMemcache();
			$memcached->host = $this->host;
			$memcached->port = $this->port;
		}

		public function fetch(array $csv){
			$this->prepare($csv, $key, $val);
			$result = $this->db()->get($key);
			if ($result == null) {
				return false;
			}
			$csv = @json_encode($result);
			$this->prepare($csv, $key, $val);
			return $val;
		}

		public function replace(array $csv){
			$this->prepare($csv, $key, $val);

			if ($this->EXPIRED !== null) {
				$expired = $this->EXPIRED;
			}

			if ($this->COMPRESS == false) {
				$compress = Memcached::NONE_COMPRESS;
			} else {
				$compress = Memcached::COMPRESS;
			}
			
			$this->db()->set($key, json_encode($val), $compress, $expired);
			$this->EXPIRED = null;
			$this->COMPRESS = true;
			return true;
		}

		public function delete(array $csv){
			$this->prepare($csv, $key, $val);
			$this->db()->delete($key);
			return true;
		}
	}
