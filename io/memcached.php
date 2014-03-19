<?php

    class IoMemcached extends WeBaseObj implements IIo {
        public $host;
        public $port;
        public function read($query){
            $conn = new Memcached();
            $conn->addServer($this->host, $this->port);
            if ($conn) {
                return $conn->get($query);
            }
        }
        public function write($data) {
            if (@$data['key']) {
                $conn = new Memcached();
                $conn->addServer($this->host, $this->port);
                if ($conn) {
                    return $conn->set($data['key'], @$data['val']);
                }
            }
            return false;
        }
        public function flush(){}
        public function pop(){}
    }
