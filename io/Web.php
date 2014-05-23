<?php
namespace std;

class Web implements IHash, ICtrl{
    private $post = new std\Post;
    private $get = new std\Get;
    private $php_inpt = new std\PhpInput;

    private $buffer = array();
    private $format = 'json';

    public function config(array $conf){
    }

    public function __isset($name){
    }

    public function __get($name){
    }

    public function __set($name, $value){
    }

    public function get($key, $preg=null){
    }

    public function set($key, $val, $preg=null){
    }

    public function allkeys(){
    }

    public function exists($key){
    }
}
