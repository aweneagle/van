<?php
    if (!defined("VAN_ROOT")) {
        define("VAN_ROOT", dirname(__FILE__) . "/");
    }
    if (!defined("VAN_TIME_NOW")) {
        define("VAN_TIME_NOW", time());
    }

    $GLOBALS["__van_objs"] = array();
    $GLOBALS["__van_objs_num"] = 1;
    $GLOBALS["__van_links"] = array();

    include VAN_ROOT . "van.class.php";
    include VAN_ROOT . "lib.func.php";


    function van_assert($assert, $errmsg){
		if ($assert !== true){
			throw new Exception($errmsg);
		}
    }
    
    function van_add_classloader($classname){
        $loader = new $classname;
        van_assert($loader instanceof IAutoload, "class is not instance of IAutoload");
        $oid = van_load($loader);
        spl_autoload_register(array(van_get($oid), "autoload"));
        return $oid;
    }
    
    function van_load($obj){
        $oid = $GLOBALS["__van_objs_num"] ;
        $GLOBALS["__van_objs_num"] ++;
        $GLOBALS["__van_objs"][$oid]["obj"] = $obj;
        $GLOBALS["__van_objs"][$oid]["nl"] = 0;
        return $oid;
    }

    function van_unload($oid){
        return van_unlink($oid);
    }

    function van_get($oid) {
        if (isset($GLOBALS["__van_objs"][$oid])) {
            return $GLOBALS["__van_objs"][$oid]["obj"];
        }
        if (isset($GLOBALS["__van_links"][$oid])) {
            $oid = $GLOBALS["__van_links"][$oid];
            van_assert(isset($GLOBALS["__van_objs"][$oid]), "empty obj for oid");
            return $GLOBALS["__van_objs"][$oid]["obj"];
        }
    }

    /* link the obj 
     * 
     * @param   $obj, mixed,   new obj or oid from van_link(), van_load()
     * @return  $oid
     */
    function van_link($linkname, $obj){
        if (is_object($obj)) {
            $oid = van_load($obj);
            return van_link($linkname, $oid);
        }

        van_unlink($linkname);

        $oid = $obj;
        if (isset($GLOBALS["__van_links"][$oid])) {
            $oid = $GLOBALS["__van_links"][$oid];
        }

        if (isset($GLOBALS["__van_objs"][$oid])) { 
            $GLOBALS["__van_links"][$linkname] = $oid;
            $GLOBALS["__van_objs"][$oid]['nl'] += 1;
        }
        return $oid;
    }

    function van_batch_link(array $cfg) {
        foreach ($cfg as $linkname => $io) {
            $obj = van_create_obj($io);
            van_link($linkname, $obj);
        }
    }

    function van_create_obj(array $cfg) {
        $io = $cfg;
        if (class_exists($io['CLASS'])) {
            $io["CLASS"] = trim($io["CLASS"]);

            $IO_CLASS = $io['CLASS'];
            unset($io['CLASS']);
            $obj = new $IO_CLASS($io);

            foreach ($io as $attr_name => $attr_value) {
                if (property_exists($obj, $attr_name)) {
                    if (is_array($attr_value) && isset($attr_value["CLASS"])) {
                        if (class_exists($attr_value["CLASS"])) {
                            $obj->$attr_name = van_create_obj($attr_value);
                        }
                    } else {
                        $obj->$attr_name = $attr_value;
                    }
                }
            }
            return $obj;
        }
        return null;
    }

    function van_unlink($linkname){
        if (isset($GLOBALS["__van_links"][$linkname])) {
            unset($GLOBALS["__van_links"][$linkname]);
            $oid = $GLOBALS["__van_links"][$linkname];

        } else {
            $oid = $linkname;
        }

        if (isset($GLOBALS["__van_objs"][$oid])) {
            $GLOBALS["__van_objs"][$oid]["nl"] -= 1;

            if ($GLOBALS["__van_objs"][$oid]["nl"] <= 0) {
                unset($GLOBALS["__van_objs"][$oid]);
            }
        }
    }

    function van_read($oid, $query){
        van_assert( ($obj = van_get($oid)) instanceof IIo, "obj is not instance of IIo" );
        return $obj->read($query);
    }

    function van_write($oid, $data){
        van_assert( ($obj = van_get($oid)) instanceof IIo, "obj is not instance of IIo" );
        return $obj->write($data);
    }

    function van_flush($oid){
        van_assert( ($obj = van_get($oid)) instanceof IIo, "obj is not instance of IIo" );
        $obj->flush();
    }

    function van_pop($oid ){
        van_assert( ($obj = van_get($oid)) instanceof IIo, "obj is not instance of IIo" );
        return $obj->pop();
    }


    
    function van_map($oid, $data ){
        van_assert( ($obj = van_get($oid)) instanceof IMap, "obj is not instance of IMap" );
        return $obj->map($data);
    }



    function van_run($oid, $params=array()){
        van_assert( ($obj = van_get($oid)) instanceof IJob, "obj is not instance of IJob" );
        return $obj->run($params);
    }



    function van_set_attr($oid, $attr_name, $attr_value){
        van_assert( ($obj = van_get($oid)) instanceof ICtrl, "obj is not instance of ICtrl" );
        van_assert( isset($obj->$attr_name), "empty attr_name");
        $obj->$attr_name = $attr_value;
    }

    function van_get_attr($oid, $attr_name){
        van_assert( ($obj = van_get($oid)) instanceof ICtrl, "obj is not instance of ICtrl" );
        van_assert( isset($obj->$attr_name), "empty attr_name");
        return $obj->$attr_name;
    }
    
    function van_add_attr($oid, $attr_name, $attr_value){
        van_assert( ($obj = van_get($oid)) instanceof ICtrl, "obj is not instance of ICtrl" );
        van_assert( isset($obj->$attr_name), "empty attr_name");
        van_assert( is_array($obj->$attr_name), "attribute is not an array");
        array_push($obj->$attr_name, $attr_value);
    }



    function van_set_state($oid, $state){
        van_assert( ($obj = van_get($oid)) instanceof IState, "obj is not instance of IState" );
        return $obj->set_state($state);
    }

    function van_get_state($oid){
        van_assert( ($obj = van_get($oid)) instanceof IState, "obj is not instance of IState" );
        return $obj->get_state();
    }



    interface IIo {
        /* read data from io
         * 
         * @param   $query, mixed
         * @return  $data, mixed
         * @err     must throw exception by van_assert() function 
         */
        public function read($query);

        /* write data into io
         * 
         * @param   $data, mixed
         * @return  $result, mixed
         * @err     must throw exception by van_assert() function
         */
        public function write($query);

        /* flush buffer
         *
         * @return  always true
         * @err     must throw exception by van_assert() function
         */
        public function flush();

        /* pop buffer
         * 
         * @return  $data, mixed
         * @err     always true
         */
        public function pop();
    }

    interface ICtrl {
    }

    interface IJob {
        /* run job
         *
         * @return  $result, mixed, will be captured by We::run();
         * @err     throw exception
         */
        public function run($param);
    }

    interface IState{
        /* set state
         *
         * @param   $state, 
         * @return  true | false
         * @err     return false
         */
        public function set_state($state);

        /* get state
         *
         * @return  null or string
         * @err     always true;
         */
        public function get_state();
    }

    interface IMap{
        /* map input
         * 
         * @param   $input, mixed
         * @return  $output, mixed
         * @err     return false
         */
        public function map($input);
    }

    interface IAutoload{
        /* auto load class, registered by spl_autoload_register()
         *
         * @param   $input, string
         * @return  true | false
         * @err     return false;
         */
        public function autoload($classname);
    }

    /* default init */
    van_add_classloader(new VanStdClassLoader);

?>
