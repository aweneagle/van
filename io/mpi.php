<?php

    class IoMpi implements IIo, IState {

        public $storage;
        public $cache;

        public function read($id){
            $res = $this->cache->read($id);
            if ($res) {
                return @igbinary_unserialize($res);
            }
            $res = $this->storage->read($id);
            $this->cache->write(array("key"=>$id, "val"=>$res));
            $res = @igbinary_unserialize($res);
            return $res;
        }

        public function write($data){
            $data["val"] = igbinary_serialize($data["val"]);
            $this->cache->write(array("key"=>$data["key"], "val"=>$data["val"]));
            $this->storage->write(array("key"=>$data["key"], "val"=>$data["val"]));
            return true;
        }

        public function flush(){
        }

        public function pop(){
        }

        public function set_state($state){/* to do */}
        public function get_state(){/* to do */}
        
    }

