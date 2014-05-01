<?php

     /* 
        this frame is used as a simple php frame, it does three things , which are : 
            1. abstract all input-output-like operations into object which named "io";
            2. abstract all request into an simple mode which named "job"
            3. abstract an environment to manager "io"s and "job"s;

        it try to reduce the dependence betweem logic modules and input-output modules as much as possible,
        so that whenever an environment must be replaced, it's still able to run the business correctly

    auth: awenzhang
    date: 2014.05.01
    */

    
    class ExpMo extends Exception {}

    /* assert
     * 
     * @return  always throw ExpMo exception if $assertion === true
     */
    function mo_assert($assertion, $errmsg, $errcode=null){
        if ($assertion !== true) {
            throw new ExpMo($errmsg, $errcode);
        }
    }

    /* environment interfaces 
     * 
     * about returns of an method and errlev's value in IEnv:
     *      1. it will be specified in comments
     *      3. logic must be implements based on the specified comment, in case one day we can replace the objects of different class equally
     */

    interface IEnv{

        /* set exception handler, it must always success
         * 
         * @return  always true
         */
        public function set_exception_handler($handler);


        /* batch link objects 
         *
         * @return  true on success , exception thrown when failed
         */
        public function batch_link(array $objs);


        /* link an obj to an linkname
         * @return  true on success , exception thrown when failed
         */
        public function link($linkname, $obj);
        

        /* get an obj
         * @return  object on success, exception thrown when failed
         */
        public function get($linkname, $class=null);


    }

    
    /* io interfaces 
        
    */

    interface IIterator extends Iterator{}

    interface IEObj {
        /* link the IEv to object , it must always success
         *
         * @return  always true
         */
        public function setEnv(IEnv $env);
    }  

    interface IQuery {
        /* do data query, like mysql, http ...
         *
         * @return  data on success, throw exception on failure
         */
        public function query($query, array $params);
    }

    interface ICtrl{
        /* open io
         *
         * @return  true on success, throw exception on failure
         */
        public function open();


        /* close io
         *
         * @return  true on success, throw exception on failure
         */
        public function close();


        /* set attribute of io 
         *
         * @return  true on success, throw exception on failure
         */
        public function set_attr($key, $val);
    }

    interface IBuffer{
        /* flush buffer data
         *
         * @return  true on success, throw exception on failure
         */
        public function flush();


        /* clean buffer data
         *
         * @return  true on success, throw exception on failure
         */
        public function clean();
    }

    interface IStack {
        /* push data
         *
         * @return  true on success, throw exception on failure
         */
        public function push($data);


        /* pop data
         *
         * @return  true on success, throw exception on failure
         */
        public function pop();
    }

    interface IHash{
        /* get data
         *
         * @param   $op, options of the "get" operation
         * @return  data on success, throw exception on failure
         */
        public function get($key, $op=array());


        /* set data
         *
         * @param   $op, options of the "set" operation
         * @return  true on success, throw exception on failure
         */
        public function set($key, $val, $op=array());


        /* del data
         *
         * @param   $op, options of the "delete" operation
         * @return  true on success, throw exception on failure
         */
        public function del($key, $op=array());
    }



    /* job interfaces */

    interface IJob{
        /* run job
         *
         * @param   $params, the params of the job routine
         * @return  true on success, throw exception on failure
         */
        public function run($params);
    }


    /* PDO mysql class
     *
     * autho: awen
     * date: 2014-02-27
     */
    class IoMysql implements IQuery, ICtrl{
        public $host ;
        public $port ;
        public $user ;
        public $passwd;
        private $conn;


        public function open(){
            $this->conn = new PDO("mysql:host=".$this->host.";port=".$this->port, $this->user, $this->passwd,  array(PDO::ATTR_PERSISTENT => false, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            if(empty($this->conn)) {
                throw new Exception("failed to conn mysql");
            }
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }


        public function close(){
            $this->conn = null;
        }

    
        public function set_attr($key, $val){
            if ($this->conn == null) {
                throw new Exception("connection is not ready");
            }
            $this->conn->setAttribute($key, $val);
        }


        public function query($sql, array $params){
            if ($this->conn == null) {
                throw new Exception("connection is not ready");
            }
            $st = $this->conn->prepare($sql);
            if ($st->execute($params) !== true) {
                throw new Exception("failed to execute query:".$query, "er:".@json_encode($st->errorInfo()));
            }
            $res = array();
            $res['ret'] = $st->fetchAll();
            $res['count'] = $st->rowCount();
            return $res;
        }
    
    }



    class IoMemcache implements IHash, ICtrl{
        public $host;
        public $port;
        private $conn;

        public function set_attr($key, $val){
            if ($this->conn == null) {
                throw new Exception("connection is not ready");
            }
            $this->conn->setOption($key, $val);
        }

        public function get($key, $op=array()){
            if ($this->conn == null) {
                throw new Exception("connection is not ready");
            }
            return $this->conn->get($key);
        }
    
        public function set($key, $val, $op=array()){
            if ($this->conn == null) {
                throw new Exception("connection is not ready");
            }

            if (empty($op)) {
                $res = $this->conn->set($key);
            } else {
                $compress = @$op['compress'];
                $expired = intval(@$op['expired']);
                $this->conn->set($key, $val, $compress, $expired);
            }
            if ($res !== true) {
                throw new Exception("failed to set value ");
            }
            return true;
        }
    }

    
    class IoSimpleMemcache extends IoMemcache {
        public function set($key, $val, $op=array()){
            parent::open();
            parent::set_

    class IoHttp implements IQuery, ICtrl{}

    class IoLogFile implements IStack, ICtrl{}

    class IoLogSocket implements IStack, ICtrl{}

    class IoCsv implements IIterator, ICtrl{}

    class IoShm implements IHash, ICtrl{}


    class IoWeb implements IHash, IBuffer{}

    class IoMpi implements IHash, IBuffer{}

    class IoLog implements IStack{}

    class IoCfg implements IHash{}

    class IoApi implements IQuery{}



    mo_link($link, $obj|$link);
    mo_unlink($link);
    mo_get($link)->open();

    $mo = new mo();
    $mo->link($link, $obj|$link);
    $mo->unlink($link);
    $mo->get($link)->open();

    $mo = mo::init();

    /* init env */
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    ...


    /* uninit env */
    $mo->unlink($link);
    $mo->unlink($link);
    $mo->unlink($link);
    $mo->unlink($link);
    $mo->unlink($link);
    ...


    /* business */
    $mo->get_ctrl($link)->open();
    $mo->get_buffer($link)->flush();
    
    $mo->get($link)->open();
    $mo->get($link)->flush();

    ?>
