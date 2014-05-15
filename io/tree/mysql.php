<?php

	/* tree modules */
	class TreeMysql implements ITree{
		private $db = new CsvMysql("127.0.0.1", "3306");
		public function get(array $branches){}
		public function set(array $subtrees){}
		public function del(array $subtrees){}
	}
