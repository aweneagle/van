<?php
	class Env implements IArr{
		private $post;	/* $_POST */
		private $get;   /* $_GET */
		private $request; /* $_REQUEST */
		private $server; /* $_SERVER */
		private $cookie; /* $_COOKIE */
		private $session; /* $_SESSION */
		private $php_input;  /* php://input */

		private $stdin = new StdWeb;
		private $stdout;
		private $stderr;

		private $io = new StdIoList();
		
		public function __get($name){
			if (property_exists($this, $name)) {
				return $this->$name;
			} else {
				throw new Exception("no property found ,class=".get_class($this).",property=".$name);
			}
		}
		public function __set($name, $val){
			if (!property_exists($this, $name)) {
				throw new Exception("no property found ,class=".get_class($this).",property=".$name);
			}

			switch ($name) {
			case 'io':
				if (! $val instanceof StdIoList) {
					throw new Exception("wrong io object, need instance of StdIoList,curr=".get_class($val));
				}
				$this->io = $val;
				break;

			case 'stdin':
			case 'stdout':
			case 'stderr':
			case 'post':
			case 'get':
			case 'request':
			case 'session':
			case 'cookie':
			case 'server':
				if (! $val instanceof IArr) {
					throw new Exception("wrong io object, it dosen't implements interface IArr,property=".$name.",class=".get_class($val));
				}
				$this->$name = $val;
				break;

			default:
				throw new Exception("unknown property, property=".$name);

			}
		}
	}

	$env = new Env;
	$env->post->reg='/^\$[0-9]+$/';
	echo $env->post;
	$env->io->role_info->get(array($uid=>array("role_info", "role_items")));

	$env->io->game_cdk->fetch(array("id"=>$cdk,"state"=>null, "expired"=>null));
	$env->io->game_cdk->delete(array("id"=>$cdk));

	$env->io->actlog->log($uid, "succ", $abc, $efg);

	foreach ($env->io->item_csv as $line) {
		echo $line['name'] . "|" . $line['uid'] ;
	}

		

	/* name rule : [function][type] */
	/* std modules */
	class StdIoList implements IArr {
		private $list = array();
		public function __get($name){
			if (!isset($this->list[$name])) {
				throw new Exception("unknown io ,name=".$name);
			}
			return $this->list[$name];
		}

		public function __set($name, $obj){
			if (!$obj instanceof IIo) { 
				throw new Exception("io obj not implements IIo, name=".$name.",class=".@get_class($obj));
			}
			return $this->list[$name] = $obj;
		}
	}
	class StdWeb implements IArr, IBuffer{
		private $post = new StdPost;	/* $_POST */
		private $get = new StdGet;   /* $_GET */
		private $request = new StdRequest; /* $_REQUEST */
		private $server = new StdServer; /* $_SERVER */
		private $cookie = new StdCookie; /* $_COOKIE */
		private $session = new StdSession; /* $_SESSION */
		private $php_input = new StdPhpInpt; /* php://input */

		private $buffer = array();

		public $PREG = '';
		public $FORMAT = 'json';
		public $IN_FORMAT = 'json';

		public function __get($name){
			if (isset($this->buffer[$name])) {
				return $this->buffer[$name];
			}
			$this->php_input->FORMAT = $this->IN_FORMAT;

			$list = array(
				$this->post, $this->get, $this->request, $this->server, $this->cookie, $this->session, $this->php_input
			);
			foreach ($list as $io) {
				$io->PREG = $this->PREG ;
				$val = $io->$name;
				if ($val !== false) {
					$this->PREG = '';
					$this->buffer[$name] = $val;
					return $val;
				}
			}
			$this->PREG = '';
			return false;
		}

		public function __set($name, $val){
			$this->buffer[$name] = $val;
		}

		public function flush(){
			switch ($this->FORMAT) {
			case 'json':
				echo json_encode($this->buffer);
			}
		}

		public function clean(){
			$tmp = $this->buffer;
			$this->buffer = array();
			return $tmp;
		}
	}

	class StdPhpInput implements IArr{
		public $PREG = null;
		public $FORMAT = 'json';
		private $input = array();
		public function __get($name){
			if (isset($this->input[$name])) {
				return $this->input[$name];
			}
			$input = file_get_contents("php://input");
			switch ($this->FORMAT) {
			case 'json':
				$input = @json_decode($input, true);
				break;

			default:
				break;
			}
			if (!empty($input)){
				$this->input = $input;
			}
			return @$this->input[$name];
		}
		public function __set($name, $value){}
	}

	class StdPregFilter implements IArr{
		public $PREG = null;
		protected $input = array();
		public function __get($name){
			if ($this->PREG !== null){
				$val = @$this->input[$name];
				if (preg_match($this->PREG, $val)){
					$return = $val;
				} else {
					$return = false;
				}
			}
			$this->PREG = null;
			return $return;
		}
		public function __set(){}
	}

	class StdPost extends StdPregFilter{
		public function __construct(){
			$this->input = &$_POST;
		}
	}

	class StdGet extends StdPregFilter{
		public function __construct(){
			$this->input = &$_GET;
		}
	}

	class StdRequest extends StdPregFilter{
		public function __construct(){
			$this->input = &$_REQUEST;
		}
	}

	class StdServer extends StdPregFilter{
		public function __construct(){
			$this->input = &$_SERVER;
		}
	}

	class StdSession extends StdPregFilter{
		public function __construct(){
			session_start();
			$this->input = &$_SESSION;
		}
		public function __destruct(){
			session_destroy();
		}
	}

	class StdCookie extends StdPregFilter{
		public function __construct(){
			$this->input = &$_COOKIE;
		}
	}


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

	class StdMemcache implements IHash, ICtrl{
		public $EXPIRED = null;
		public $COMPRESS = null;
		public function __get(){}
		public function __set(){}
		public function get($key){}
		public function set($key, $val){}
	}


	/* log modules */
	class LogFile implements IStream extends LogBase{
		private $path = "/data/debug";
		private $split = "|";
		public function read(){}
		public function write($line){
			$fp = @fopen($this->path, "a");
			if (!$fp) {
				throw new Exception("failed to open file,file=".$this->path.",err=".@json_encode(error_get_last()));
			}
			if (fwrite($fp, $line) === false){
				throw new Exception("failed to write file,file=".$this->path.",err=".@json_encode(error_get_last()));
			}
			if (fclose($fp) === false) {
				throw new Exception("failed to close file,file=".$this->path.",err=".@json_encode(error_get_last()));
			}
			return true;
		}
		public function log(){ return $this->write(implode("|", func_get_args()));}
	}

	class LogSocket implements IStream extends LogBase{
		private $path = "socket:/var/someone.socket";
		public function read(){}
		public function write($line){}
		public function log(){}
	}


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

	class CsvFile implements ICsv{
		public function fetch(array $csv){}
		public function replace(array $csv){}
		public function delete(array $csv){}
	}

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

	class CsvCachedDb implements ICsv{
		private $storage = new CsvMysql;
		private $cache = new CsvMemcached;
		public function fetch(array $csv){}
		public function replace(array $csv){}
		public function delete(array $csv){}
	}



	/* tree modules */
	class TreeMysql implements ITree{
		private $db = new CsvMysql("127.0.0.1", "3306");
		public function get(array $branches){}
		public function set(array $subtrees){}
		public function del(array $subtrees){}
	}

	class TreeMemcahe implements ITree{
		private $db = new CsvMemcached("127.0.0.1", "3307");
		public function get(array $branches){}
		public function set(array $subtrees){}
		public function del(array $subtrees){}
	}

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

	class TreeCacheDb implements ITree{
		private $cache = new TreeMemcached;
		private $db = new TreeMysql;
		public function get(array $branches){}
		public function set(array $subtrees){}
		public function del(array $subtrees){}
	}




	/* "stream-style" and "block-style" are the base io styles(excepct the "event-style" io)*/
	interface IIo{
	}

	interface IStream extends IIo{
		public function read();
		public function write($data);
	}

	interface IBlock extends IIo{
		public function read($query, $params);
		public function write($query, $params);
	}

	/* io buffer */
	interface IBuffer extends IIo{
		public function flush();
		public function clean();
	}


	/* io attribute */
	interface IArr extends IIo{
		public function __get();
		public function __set();
	}

	interface ICtrl extends IArr{
	}


	/* io data formate */

	interface IHash extends IIo{
		public function get($key);
		public function set($key, $val);
	}

	interface ITree extends IIo{
		public function get(array $subtrees);
		public function set(array $subtrees);
		public function del(array $subtrees);
	}

	interface ICsv extends IIo{
		public function fetch(array $csv);
		public function replace(array $csv);
		public function delete(array $csv);
	}




