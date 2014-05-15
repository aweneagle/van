<?php
	class StdMemcache implements IHash, ICtrl{
		private $EXPIRED = null;
		private $COMPRESS = null;
        private $host = null;
        private $port = null;
		public function __get(){}
		public function __set(){}
		public function get($key){}
		public function set($key, $val){}
	}
