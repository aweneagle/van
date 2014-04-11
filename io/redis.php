<?php

    class IoRedis implements IIo {
        public $host;
        public $port;
        public function read($index){
            $conn = new Redis();
            if ($conn->connect($this->host, $this->port)) {
                return $conn->get($index);
            } else {
                return false;
            }
        }

        public function write($data){
            $conn = new Redis();
            if (@$data['key'] && $conn->connect($this->host, $this->port)) {
                return $conn->set($data['key'], @$data['val']);
            }
            return false;
        }

        public function flush(){}
        public function pop(){}
    }
