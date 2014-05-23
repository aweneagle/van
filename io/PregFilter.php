<?php
namespace std;

abstract class PregMatch implements  IHash {
    protected $input = array();
	public function get($key, $preg=null){}
	public function set($key, $val, $preg=null){}
	public function exists($key, $options=null){}
    public function all(){}
}
