<?php

    /* PDO mysql class
     *
     * autho: awen
     * date: 2014-02-27
     */
    class IoMysql  implements IIo,ICtrl,IState{
        public $host ;
        public $port ;
        public $user ;
        public $passwd;

        private $conn;

        public function read($pdo_query){
            return $this->_execute($pdo_query, "read");
        }

        public function write($pdo_query){
            return $this->_execute($pdo_query, "write");

        }

        public function flush(){}
        public function pop(){}

        public function get_state(){
            /* to do */
            return "connected";
        }

        public function set_state($state){
            /* to do */
        }

        /*
        public function read("select * from $db.$table where id=$id"){}
        */

        /* get the array's most deepest dimention
         * 
         *@param $arr   
         *@return $dimention, int , if $arr is empty, $dimention = 0;
         */
        private function _arr_dimention(array $arr){
           $dimention = 0;
           foreach ($arr as $k => $v) {
               if (!is_array($v) && $dimention == 0) {
                   $dimention = 1;
                   
               } else if (is_array($v)) {
                   //step into next level
                   $sub_depth = 1 + $this->_arr_dimention($v);
                   $dimention = ($sub_depth > $dimention) ? $sub_depth : $dimention;
               }
           }
           return $dimention ;
        }

        private function _result($st, $type){
            switch ($type) {
                case "read":
                    return $st->fetchAll();

                case "write":
                    return $st->rowCount();

                default :
                    van_assert(false,  "unknown mysql io type", "type:".$type);
            }
        }

        private function _connect(){
            $this->conn = new PDO("mysql:host=".$this->host.";port=".$this->port, $this->user, $this->passwd,  array(PDO::ATTR_PERSISTENT => false, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            van_assert(!empty($this->conn), "failed to conn mysql");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        /* mysql execute query
         *
         *@param    pdo_query,  mix,  
         *          1. pdo_query=string,  it will be execute as usuall 
         *          2. pdo_query=array( "query"=>query_string, "columns"=>array( ?,?,...) ),  it contents of one execute instruction
         *          3. pdo_query=array( "query"=>query_string, "columns"=>array( array(?,?,...), array(?,?,...) ), it contents of several execute instructions 
         *@param    type, string,  "read"(select statement)  or "write" (update statement)
         *@return   array( no => datas )
         */
        private function _execute($pdo_query, $type){
            $this->_connect();
            // simple query string 
            if (is_string($pdo_query)) {
                $query = $pdo_query;
                $st = $this->conn->prepare($query);
                $res = $st->execute();

                van_assert($st->execute() === true,  "failed to execute query","query:".$query);

                return $this->_result($st, $type);

            } else {

                van_assert(@$pdo_query['query'] != null,  "empty query pdo query string ");
                van_assert(is_array(@$pdo_query['columns']),  "wrong columns for pdo");

                $query = $pdo_query['query'];
                
                // prepared query string
                $st = $this->conn->prepare($query);
                if ($this->_arr_dimention($pdo_query['columns']) == 1) {

                    van_assert($st->execute($pdo_query["columns"]) === true,  "failed to execute query:".$query, "er:".@json_encode($st->errorInfo()));

                    return $this->_result($st, $type);

                } else if ($this->_arr_dimention($pdo_query["columns"]) == 2) {
                    $res = array();
                    foreach ($pdo_query["columns"] as $i => $col) {

                        van_assert($st->execute($col) === true,  "failed to execute query,query:".$query);

                        $res[$i] = $this->_result($st, $type);
                    }
                    return $res;
                }
            }


        }
    }
