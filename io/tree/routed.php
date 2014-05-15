<?php


	class TreeRoutedDb implements ITree{
		private $db = array( 
			"info" => (new TreeMysql, new TreeMysql, new TreeMysql ),
			"map" => (new TreeMysql, new TreeMysql, new TreeMysql ),
			"items" => (new TreeMysql, new TreeMysql, new TreeMysql ),
		);
		public function get(array $branches){}
		public function set(array $subtrees){}
		public function del(array $subtrees){}
	}
