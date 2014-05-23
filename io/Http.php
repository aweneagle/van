<?php
namespace std;

class Http implements IBlock{
    private $host = null;
    private $port = "80";
    private $curl_options = array();

    public function query($uri, $params=null, $method=null){
    }

    public function __isset($name){
    }

    public function __get($name){
    }

    public function __set($name, $value){
    }

}

