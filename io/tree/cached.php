<?php

	class TreeCacheDb implements ITree{
		private $cache = new TreeMemcached;
		private $db = new TreeMysql;
		public function get(array $branches){}
		public function set(array $subtrees){}
		public function del(array $subtrees){}
	}
