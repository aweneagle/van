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


    class IoMpiMysql implements IIO{ 
        public $db;
        public $table;
        public $field = "data";
        public $id_len = 34;    //id length, = length(bin) * 2
        public $mysql = "pepper_mysql";  //link to an mysql io

        private function get_table_no($key){
            return strtolower(substr($key, -2));
        }

        public function read($uid){
            $table_no = $this->get_table_no($uid);
            $uid = str_pad($uid, $this->id_len, "0");
            $uid = pack("H*",$uid);
            $data = van_read(
                    $this->mysql, 
                    array(
                        "query"=>"select " . $this->field . " from " . $this->db . "." . $this->table .$table_no. " where id=?" ,
                        "columns"=>array($uid)
                        )
                    );

            if (empty($data)){ 
                return null;
            } else {
                return $data[0][0];
            }
        }

        public function write($data){
            $table_no = $this->get_table_no($data["key"]);
            $data["key"] = str_pad($data['key'], $this->id_len, "0");
            $data["key"] = pack("H*", $data["key"]);
            return van_write(
                    $this->mysql, 
                    array(
                        "query" => "replace into " . $this->db . "." . $this->table .$table_no." set " . $this->field . "=?" . ", id=?",
                        "columns" => array( $data["val"], $data["key"] )
                        )
                    );
        }

        public function flush(){}
        public function pop(){}
    }
