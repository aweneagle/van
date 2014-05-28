<?php

/* stdandard web input/output
 *
 * $web->IN_FORMAT = 'xml';
 * $web->FORMAT = 'json';
 *
 * $web->PREG = '/^[0-9]{10}$/';
 * $all = $web->all();
 * $web->output = $all;
 */

class StdWeb implements IInput, IBuffer{
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

	public function all(){
	}

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
