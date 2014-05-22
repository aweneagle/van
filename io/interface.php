<?php

/* "stream-style" and "block-style" are the base io styles(excepct the "event-style" io)*/
interface IIo{
}


/* private properties accessable
 */
interface IArr{
	/* error: return false and print out warning info ; throw exception only when illeagle $val is passed in*/
	public function __set($name, $val);
	/* error: return false and print out warning info */
	public function __get($name);
	/* return true or false */
	public function __isset($name);
}

interface IInput extends IArr {
	public function all();
}

abstract class IEvent{
	const STOP = 0x01;
	const PAUSE = 0x02;
	const RESUME = 0x03;
	public $sleep = 1;  /* seconds */
	public function set_handler($event, $func){}
	public function run($func){}
}

interface IStream extends IIo{
	public function read();
	public function write($data);
}


/* random access data storage 
 * 
 * like a db, a http interface, ...
 */
interface IBlock extends IIo{
	public function query($query, $params = null);
}

/* buffered io
 */
interface IBuffer extends IIo{
	public function flush();
	public function clean();
}

/* key->value struct
 *
 * it is simply a hash table
 */
interface IHash extends IIo{
	public function get($key);
	public function set($key, $val);
	public function exists($key);
}

/* "tree" struct
 *
 * contening several "subtrees" , which consists of some other subtrees or "leaves"
 *
 * when operating on an tree , it will 
 * 	1. update the specified leaves if needed ; 
 * 	2. return an substree state as the result, usually the state of the specified leaves;
 */
interface ITree extends IIo{
	public function get(array $subtrees);
	public function set(array $subtrees);
	public function del(array $subtrees);
	public function exists(array $subtrees);
}

	
/* csv row, a set of "key"=>"value" fields , forexample : array("id"=>123, "name"=>"foo", "sex"=>1)
 *
 * when operating on an specified csv row, it acts and only acts on this row , but will not effect on other rows at all;
 * forexample, when updating ("id"=>123, "name"=>"foo", "sex"=>1), it will not effect the other row ("id"=>124, "name"=>"foo", "sex"=>1);
 * it's your duty to decide which two rows are the same 
 */
interface ICsv extends IIo{
	public function fetch(array $csv);
	public function update(array $csv);
	public function delete(array $csv);
	public function count();
}


