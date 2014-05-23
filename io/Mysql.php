<?php
namespace std;

class Mysql implements IBlock, ICtrl{
    private $host;
    private $port;
    private $username;
    private $passwd;
    private $dbname;

    public function query($query, $params=null, $pdo_options=null){
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
