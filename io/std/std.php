<?php

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

