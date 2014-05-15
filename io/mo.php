<?php

     /* 
	it is used as an package of json document tools and csv document tools 


    date: 2014.05.01
      */


/* operation on a json document 
 *
 * @param	$ref, reference of an json tree, for example :
 *
 * 	$ref = array (
 * 		"a" => array(
 * 			"b" => null,
 * 			"c" => null
 * 			)
 * 		)
 *
 * 	will reference into the two json documents :
 * 	$doc["a"]["b"]   
 * 	$doc["a"]["c"]
 * 	
 * @return	$doc, in the same tree struct as $ref
 *
 * let's say we have an json document $DOC, and the class JsonExample implements IJson:
 *
 * 	$DOC = array("a"=>array("b"=>1, "c"=>array("something"), "d"=>"else"));
 *      $json = new JsonExample($DOC);
 *
 * then we should operate the "get" method like this , to fetch the $DOC['a']['b'] and $DOC['a']['c']:
 *
 *      $ref = array("a"=>array("b"=>null, "c"=>null));
 *      $res = $json->get($ref);
 *
 * and the result should always be :
 * 	array("a" => array(
 * 		"b" => 1,
 * 		"c" => array("something")
 * 		)
 * 	)
 */
interface IJson {
	/* get json document elements
	 *
	 * @param	$ref, reference tree struct, for example array("a"=>array("b"=>null, "c"=>null)), the "null"s is not a must,
	 * 		they should always be ignored 
	 * @return	document, must keeps the same tree struct as $ref, like array("a"=>array("b"=>1, "c"=>"awen"))  in this example
	 */
	public function get(array $ref);


	/* update json  document elements 
	 *
	 * @param	$doc, document , the same format as $ref,  
	 * 		except that all "leaves" in $doc "tree" are meanful and should be update into the Json document
	 * 		if "branches" in $ref don't exists, it will be created autolly
	 * @return	always true
	 */
	public function set(array $doc);

	/* delete json document elements
	 *
	 * @param	$ref, reference tree struct, for example array("a"=>array("b"=>null, "c"=>null)), the "null"s is not a must,
	 * 		they should always be ignored 
	 * @return	always true
	 */
	public function del(array $ref);

	/* check if the node exists or not 
	 *
	 * @return	true  if and only if all "leaves" in $ref are found ,
	 * 		false, other wise
	 */
	public function exists(array $ref);
}


/* operation on a csv document 
 *
 * all operations below should obey the rule :
 * 	@param 	$row,	 array( fieldname => value ), primary key must be provied
 */
interface ICsv {

	/* exceptions define */
	const EXP_UNKNOWN = 0x0010;		/* unknown exception */
	const EXP_NONE_PRI_KEY = 0x0011;	/* the param $row lack of primary key */
	const EXP_WRONG_FIELDS = 0x0012;	/* the param $row has the wrong fields */
	const EXP_NONE_DATA = 0x0013;	/* data not exists, usually thrown by fetch(), update(), delete()*/
	const EXP_WRONG_DATA = 0x0014;
	const EXP_EXISTS_DATA = 0x0015;	/* data already exists , usually thrown by insert() */

	/* fetch a row
	 *
	 * @return  contents of the row, always succss
	 */
	public function fetch(array $row);

	/* update a row 
	 *
	 * @return  always true
	 */
	public function update(array $row);

	/* insert a row
	 *
	 * @return  always true
	 */
	public function insert(array $row);

	/* delete a row
	 *
	 * @return  always true
	 */
	public function delete(array $row);

	/* return the total number of csv rows
	 *
	 * @return  always success
	 */
	public function count();
}

interface IHash {
	public function get($key);
	public function set($key, $val);
}

/*  operation on a stream io 
 */
interface IStream {
	/* read from io
	 *
	 * @return   contents of stream, always success 
	 */
	public function read();

	/* write into stream 
	 *
	 * @return	always true
	 */
	public function write($contents);
}


/* operation on a buffer io
 */
interface IBuffer {
	/* flush out the buffered contents 
	 * 
	 * @return 	always true;
	 */
	public function flush();

	/* clean out the buffered contents 
	 *
	 * @return	always true;
	 */
	public function clean();
}



/* csv db
 *
 * required : PDO extension installed 
 * provide simple table operation , like fetch row, update row, insert row, and delete row
 */

class CsvDb implements ICsv {
	public $host;
	public $port;
	public $user;
	public $passwd;
	public $db;		/* db name */
	public $table;	/* table name */
	public $key;	/* primary key */
	public $options = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_PERSISTENT => false,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	);

	private function setAttributes($pdo){
		foreach ($this->options as $key=>$val) {
			$pdo->setAttribute($key, $val);
		}
	}

	private function prepare(array $fields, $db, &$dbname, &$tablename){
		if (!isset($fields[$this->key])) {
			throw new Exception("no primary key found,need:".$this->key, ICsv::EXP_NONE_PRI_KEY);
		}
		$conn = "mysql:" . $this->host . ";" . $this->port ;
		$db = new PDO($conn, $this->user, $this->passwd);
		$this->setAttributes($db);
		$dbname = $this->db;
		$tablename = $this->table;
	}

	private function execute($db, $sql, array $params, $op="UPDATE"){
		$st = $db->prepare($sql);
		$res = $st->execute($params);
		if ($res !== true) {
			throw new Exception("failed to execute sql statement, sql=" . $sql . "er:".@json_encode($st->errorInfo()), ICsv::EXP_UNKNOWN);
		}
		switch ($op){
		case 'UPDATE':
			return $st->rowCount();
		case 'FETCH':
			return $st->fetchAll(PDO::FETCH_ASSOC);
		}
	}


	public function fetch(array $fields){
		$this->prepare($fields, $db, $dbname, $tablename);
		$sql = "SELECT " . implode(",", $fields) . " FROM " . $dbname . "." . $tablename .
			" WHERE " . $this->key . "=?" ;
		$row = $this->execute($db, $sql, array($fields[$this->key]), "FETCH");
		if (empty($row)) {
			throw new Exception("no data found", ICsv::EXP_NONE_DATA);
		}
		return $row;
	}

	public function update(array $fields){
		$this->prepare($fields, $db, $dbname, $tablename);
		$sql = "UPDATE " . $dbname . "." . $table . " SET (" . implode(",", $fields) . ") VALUES (" . join(array_fill(0,count($fields),"?")) . ")" .  
			" WHERE " . $this->key . "=?" ;
		$values = array_values($fields);
		array_push($values, $fields[$this->key]);
		$rownum = $this->execute($db, $sql, $values, "UPDATE");
		if ($rownum >= 1) {
			return true;
		} else {
			throw new Exception("failed update fields=".json_encode($fields), ICsv::EXP_NONE_DATA);
		}
	}

	public function insert(array $fields){
		$this->prepare($fields, $db, $dbname, $tablename);
		$sql = "INSERT INTO " . $dbname . "." . $table . " VALUES (" . join(array_fill(0,count($fields),"?")) . ")"   ;
		$rownum = $this->execute($db, $sql, array_values($fields), "UPDATE");
		if ($rownum >= 1) {
			return true;
		} else {
			throw new Exception("failed insert fields=".json_encode($fields), ICsv::EXP_EXISTS_DATA);
		}
	}

	public function delete(array $fields){
		$this->prepare($fields, $db, $dbname, $tablename);
		$sql = "DELETE FROM " . $dbname . "." . $table . " WHERE " . $this->key . "=?"  ;
		$rownum = $this->execute($db, $sql, array($fields[$this->key]), "UPDATE");
		if ($rownum >= 1) {
			return true;
		} else {
			throw new Exception("failed delete fields=".json_encode($fields), ICsv::EXP_NONE_DATA);
		}
	}

	public function count(){
		$this->prepare($fields, $db, $dbname, $tablename);
		$sql = "SELECT COUNT(*) FROM " . $dbname . "." . $table ;
		$res = $this->execute($db, $sql, array(), "FETCH");
		return $res[0]["COUNT(*)"];
	}
}

class CsvMemcached implements ICsv {
	public $host;
	public $port;
	public $key;

	public function count(){
		/* althougnt Memcached has method Memcached::getAllKeys(), we decide not to use it */
		return false;
	}

	private function setAttributes($conn){
		/* to do */
	}

	private function prepare(array $fields, $db){
		if (!isset($fields[$this->key])) {
			throw new Exception("no primary key found,need:".$this->key, ICsv::EXP_NONE_PRI_KEY);
		}
		$db = new Memcached();
		$db->addServer($this->host, $this->port);
		$this->setAttributes($db);
	}

	private function read($row, $db){
		$data = $db->get($row[$this->key]);
		if ($data == null) {
			throw new Exception("no data found ", ICsv::EXP_NONE_DATA);
		} else {
			$data = @json_decode($data, true);
			if (!is_array($data)) {
				throw new Exception("wrong csv data struct", ICsv::EXP_WRONG_DATA);
			}
			return $data;
		}
	}

	private function write($row, $db){
		$data = $db->set($row[$this->key], json_encode($row));
		if ($data !== true) {
			throw new Exception("failed to set data", ICsv::EXP_UNKNOWN);
		} else {
			return true;
		}
	}

	private function fetch_csv(array $doc, array &$row){
		foreach ($row as $key => $val) {
			if (!isset($doc[$key])) {
				throw new Exception("[fetch]no elements for key=".$key, ICsv::EXP_WRONG_FIELDS);
			}
			$row[$key] = $doc[$key];
		}
	}

	private function update_csv(array &$doc, array $row){
		foreach ($row as $key => $val) {
			if (!isset($doc[$key])) {
				throw new Exception("[update]no elements for key=".$key, ICsv::EXP_WRONG_FIELDS);
			}
			$doc[$key] = $row[$key];
		}
	}

	public function fetch(array $row){
		$this->prepare($row, $db);
		$data = $this->read($row, $db);
		$this->fetch_csv($data, $row);
		return $row;
	}

	public function update(array $row){
		$this->prepare($row, $db);
		$data = $this->read($row, $db);
		$this->update_csv($data, $row);
		$this->write($data);
		return true;
	}

	public function insert(array $row){
		$this->prepare($row, $db);
		try {
			$data = $this->read($row, $db);
			throw new Exception("data alread exists, maybe you should delete it first", ICsv::EXP_EXISTS_DATA);
		} catch (Exception $e) {
			$this->write($row[$this->key], json_encode($row));	
			return true;
		}
	}

	public function delete(array $row){
		$this->prepare($row, $db);
		try {
			$data = $this->read($row, $db);
			$res = $db->delete($row[$this->key]);
			if ($res !== true) {
				throw new Exception("failed to delete row", ICsv::EXP_UNKNOWN);
			}
			return true;
		} catch (Exception $e) {
			throw new Exception("data not exists", ICsv::EXP_NONE_DATA);
		}
	}
}

/* json db
 *
 */
class JsonDB extends JsonStorage{
	private $doc;	/* document */
	public $host;
	public $port;
	public $user;
	public $passwd;
	public $db;
	public $table;
	public $options = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_PERSISTENT => false,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	);

	protected function getCsvDb(){
		$db = new CsvDb();
		$db->host = $this->host;
		$db->port = $this->port;
		$db->user = $this->user;
		$db->passwd = $this->passwd;
		$db->db = $this->db;
		$db->table = $this->table;
		$db->key = $this->key;
		$db->options = $this->options;
		return $db;
	}
}

class JsonMemcached extends JsonStorage{
	public $host;
	public $port;
	public $key;

	protected function getCsvDb(){
		$mem = new CsvMemcached();
		$mem->host = $this->host;
		$mem->port = $this->port;
		return $mem;
	}
}

class JsonMultiDb implements IJson{
	public $old_dbs = array();
	public $new_dbs = array();
	public $storage_router = null;
	public function route_storage($key, $type="old"){
	}
	public function get(array $ref) {
		$newdoc = $this->fetch_from_new($ref);
		$olddoc = $this->fetch_from_old($ref);
		$tomove = array();
		foreach ($newdoc as $id => $val) {
			if ($val === null && $olddoc[$id] !== null) {
				$tomove[$id] = $newdoc[$id] = $olddoc[$id];
			}
			abc_json_fetch($newdoc[$id], $ref[$id]);
		}
		$this->set_to_new($tomove);
		$this->delete_from_old($tomove);
		return $ref;
	}

	public function set(array $doc){
		$newdoc = $this->fetch_from_new($ref);
		$olddoc = $this->fetch_from_old($ref);
		$tomove = array();
		foreach ($newdoc as $id => $val) {
			if ($val === null && $olddoc[$id] !== null) {
				$tomove[$id] = $newdoc[$id] = $olddoc[$id];
			}
			abc_json_update($newdoc[$id], $ref[$id]);
		}
		$this->set_to_new($newdoc);
		$this->delete_from_old($tomove);
		return true;
	}

	public function del(array $ref){
		$todelete = array();
		foreach ($ref as $id => $val) {
			if ($val === null) {
				$todelete[$id] = null;
				unset($ref[$id]);
			}
		}
		$this->delete_from_old($todelete);
		$this->delete_from_new($todelete);
		$newdoc = $this->fetch_from_new($ref);
		$olddoc = $this->fetch_from_old($ref);
		$tomove = array();
		foreach ($newdoc as $id => $val) {
			if ($val === null && $olddoc[$id] !== null) {
				$tomove[$id] = $newdoc[$id] = $olddoc[$id];
			}
			abc_json_delete($newdoc[$id], $ref[$id]);
		}
		$this->set_to_new($newdoc);
		$this->delete_from_old($tomove);
	}
}


abstract class JsonStorage implements IJson {
	abstract protected function getCsvDb();
	public function allkey(){
		return $this->getCsvDb()->count();
	}

	private function read(array $ref){
		$set = array();
		foreach ($ref as $id => $doc) {
			$doc = $this->getCsvDb()->fetch(array("id"=>$id, "data"=>null));
			$set[$id] = @json_decode($doc['data'], true);
		}
		return $set;
	}

	private function write(array $doc){
		foreach ($doc as $id => $val) {
			$this->getCsvDb()->update(array("id"=>$id, "data"=>json_encode($val)));
		}
	}

	public function exists(array $ref){
	}

	public function get(array $ref){
		$doc = $this->read($ref);
		return abc_json_fetch($doc, $ref);
	}

	public function set(array $doc){
		$old_doc = $this->read($doc);
		abc_json_update($old_doc, $doc);
		$this->write($old_doc);
		return true;
	}

	public function del(array $ref){
		$doc = $this->read($ref);
		$doc = abc_json_del($doc, $ref);
		foreach ($ref as $id => $val) {
			if (!isset($doc[$id])) {
				$this->getCsvDb()->delete(array("id"=>$id));
			}
		}
		$this->write($doc);
		return true;
	}
}
