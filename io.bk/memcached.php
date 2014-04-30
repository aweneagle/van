<?php

    class IoMemcached implements IIo {
        public $host;
        public $port;
        public function read($query){
            $conn = new Memcached();
            $conn->addServer($this->host, $this->port);
            $conn->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
            $conn->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
            $conn->setOption(Memcached::OPT_TCP_NODELAY, true);
            if ($conn) {
                return $conn->get($query);
            }
        }
        public function write($data) {
            if (@$data['key']) {
                $conn = new Memcached();
                $conn->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
                $conn->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
                $conn->setOption(Memcached::OPT_TCP_NODELAY, true);
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
