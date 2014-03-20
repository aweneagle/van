<?php

    class IoMpi implements IIo, IState {

        public $storage;
        public $cache;

        public function read($id){
            $res = $this->cache->read($id);
            if ($res) {
                return $res;
            }
            $res = $this->storage->read($id);
            $this->cache->write(array("key"=>$id, "val"=>$res));
            return $res;
        }

        public function write($data){
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
        public $field;
        public $mod;   //multi tables
        public $mysql;  //link to an mysql io

        private function get_table_no($key){
            return "01";
        }

        public function read($uid){
            $uid = pack("H*",$uid);
            $table_no = $this->get_table_no($uid);
            $data = van_read(
                    $this->mysql, 
                    array(
                        "query"=>"select " . $this->field . " from " . $this->db . "." . $this->table .$table_no. " where id = ?" ,
                        "columns"=>array($uid)
                        )
                    );

            if (empty($data)){ 
                return null;
            } else {
                return $data[0];
            }
        }

        public function write($data){
            $data["key"] = pack("H*", $data["key"]);
            $table_no = $this->get_table_no($data["key"]);
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
