<?php

	class TreeMemcahe implements ITree{
		private $db = new CsvMemcached("127.0.0.1", "3307");
		public function get(array $branches){}
		public function set(array $subtrees){}
		public function del(array $subtrees){}
	}
