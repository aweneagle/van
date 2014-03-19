<?php
    class IoLogfile implements IIo {
        public $path = "/tmp/example";
        public function __construct($path){
            $this->path = $path;
        }
        public function write($line){
            @file_put_contents($this->path, $line);
        }
        public function read($query){}
        public function flush(){}
        public function pop(){}
    }
