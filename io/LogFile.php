<?php
namespace std;
class LogFile implements ILog, ICtrl{
    private $fd = new SplFile;
    private $path;
    private $mod;
    private $spliter = "|";
    private $time_formate = "%Y%m%d-%H%I%S";

    public function log(){
    }

    public function config(array $conf){
    }

    public function __isset($name){
    }

    public function __get($name){
    }

    public function __set($name, $value){
    }
    
}
