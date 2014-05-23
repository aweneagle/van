<?php
namespace std;

class Config implements IHash, ICtrl{
    private $shm_key = 0x00001;
    
    public function get($key, $op=null){
    }

    public function set($key, $val, $op=null){
    }

    public function del($key, $op=null){
    }

    public function allkeys(){
    }

    public function __isset($name){
    }

    public function __get($name){
    }

    public function __set($name, $value){
    }
}
