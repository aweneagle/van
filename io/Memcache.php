<?php
namespace std;

class Memcache implements IHash, ICtrl{
    private $host;
    private $port;
    private $options = array();

    public function get($key, $options=null){
    }

    public function set($key, $value, $expired=0){
    }

    public function config(array $conf){
    }

    public function __get($name){
    }

    public function __set($name, $value){
    }

    public function __isset($name){
    }
}
