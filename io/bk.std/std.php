<?php
/*
 * the unknown properties solution 
 *
 * $obj = new IArr;
 *
 * // "setter" 
 * // case 1.	throw exception when properity not found 
 * foreach ($props as $key => $val) {
 * 	try {
 * 		$obj->$key = $val;	// if $key not found, exceptions will be thrown 
 * 	} catch (Exception $e) {
 *		switch ($e->getCode()) {
 *			case IArr::ERR_KEY_NOT_EXISTS:	//"key not exists" error
 *			case IArr::ERR_VAL_ILLEAGLE:	//"value illeagle" error
 *		}
 * 	}
 * }
 *
 * // case 2.	warning and return false
 * foreach ($props as $key => $val) {
 * 	try {
 * 		$res = (@$obj->$key = $val);
 * 	} catch (Exception $e) {
 * 		//the "key exists , but illeagle value" error will be thrown out 
 * 		continue;
 * 	}
 * }
 *
 *
 * // "getter" 	warning and return false
 * foreach ($props as $key => $val) {
 * 	$res = @$obj->$val;
 * }
 */


/* standard io list
 *
 * $il->db = new std\Mysql;
 * @$il->wrong_prop = "wrong";	//warning
 * $il->db = new Mysql;	// thrown exception 
 *
 * $il->db->host = "localhost";
 * $il->db->query("select * from db.table where id=?", array($uid));
 */

class StdIoList implements IArr {
	private $list = array();
	public function __get($name){
		if (!isset($this->list[$name])) {
			php_error("unknown io ,name=".$name);
			return false;
		}
		return $this->list[$name];
	}

	public function __set($name, $obj){
		if (!$obj instanceof IIo) { 
			php_error("io obj not implements IIo, name=".$name.",class=".@get_class($obj));
			return false;
		}
		return $this->list[$name] = $obj;
	}

	public function __isset($name){
		return isset($this->list[$name]);
	}
}


/* standard preg filter
 *
 * let's say the input is from $_POST: 
 *
 * $input = new StdInput($_POST);
 * $input->preg('/^\w+$/');	// '/^\w+$/'  will be set into $input->PREG
 * echo $input->preg;		// $_POST['preg'];
 * echo $input->preg();		// $input->preg(): do nothing but echo the current $input->PREG
 * echo $input->preg(false);	// $input->preg(false): set false into $input->PREG 
 *
 * $input->format('JSON');	
 * echo $input->format;		// echo $_POST['format'];
 * echo $input->format();	// ehco 'JSON'
 *
 * $input->preg('/^\w+$/');
 * echo $input->all();		// return all inputs which match the '/^\w+$/' 
 *
 */
class StdPregFilter implements IArr{
	private $PREG = false;
	protected $input = array();

	/* set and get the preg expression 
	 *
	 * when opening the preg-matching with this method, it will affect the following methods' behavior:
	 *   self::__get()
	 *   self::__set()
	 *   self::__isset()
	 *   self::all()
	 *
	 * @param	$pattern , mixed , three possible value is accepted 
	 * 		1. null,   method do nothing , but just return the value of PREG
	 * 		2. false,  close the preg-matching
	 * 		3. not null pattern string, open the preg-matching
	 *
	 * @return 	always return the value of PREG
	 */
	public function preg($pattern=null){
	}

	/* fetch all input 
	 *
	 * with preg-matching opening (by self::preg() method), only the matched inputs will be returned 
	 */
	public function all(){
	}

	/* fetch the specified input
	 *
	 */
	public function __get($name){
		if ($this->PREG !== false){
			$val = @$this->input[$name];
			if (preg_match($this->PREG, $val)){
				$this->PREG = null;
				$return = $val;
			} else {
				$return = false;
			}
		}
		return $return;
	}

	/* set the specified input
	 *
	 */
	public function __set($name, $val){
	}

	/* check whether the specified input exists or not
	 * 
	 */
	public function __isset($name){
	}
}

class StdWeb extends StdPregFilter{
	private $inputs = array();
	public function __construct($format){
		$this->inputs = array(new StdPhpInput($format), new StdPost, new StdGet, new StdSession, new StdCookie);
	}

	public function __get($name){
		foreach ($this->inputs() as $io) {
			if (isset($io->$name)) {
				return $io->$name;
			}
		}
	}
}

class StdPhpInput extends StdPregFilter{
	public function __construct($format){
		switch ($format) {
		case 'json':
			$this->input = json_decode(file_get_contents("php://input"), true);
			break;

		case 'simple_xml':
			$this->input = json_decode(json_encode(simplexml_load_file("php://input")), true);
			break;
		}
	}
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

