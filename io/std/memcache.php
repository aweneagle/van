<?php
/* standard memcache
 *
 */
class StdMemcache implements IHash, ICtrl{
	private $expired = null;
	private $compress = null;
	private $host = null;
	private $port = null;
	private $options = array();

	public function __get(){}
	public function __set(){}
	public function get($key){}
	public function set($key, $val){}
}
