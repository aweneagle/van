<?php
namespace std;

class Socket implements IStream, ICtrl{
    private $reuse = false;
    private $conn = null;

    private $host = null;
    private $port = null;
    private $protocal = 'TCP';
    
    public function read(){
    }

    public function write($str){
    }

    public function config(array $conf){
    }

    public function __isset($name){
    }

    public function __set($name, $value){
    }

    public function __get($name){
    }
}
