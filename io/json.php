<?php

    class IoJson implements IIo{
        public function read($key){
            return @$this->data[$key];
        }

        public function write($data){
            return $this->data[@$data["key"]] = @$data["val"];
        }

        public function flush(){
            echo @json_encode($this->data);
        }

        public function pop(){
            $tmp = $this->data;
            $this->data = array();
            return $tmp;
        }
    }
