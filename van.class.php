<?php

    /************** base classes **********/
    class VanStdClassLoader implements IAutoload {

	public $loaded = array();
        public $class_roots = array(VAN_ROOT);

        /* auto load class
         * 
         * @param   $classname, string
         * @return  $obj, object
         * @err     return false;
         */
        public function autoload($classname){
            $name = $classname;
            van_map_class2path($name, $path, $classfile);
            $path = trim($path, "/");
            foreach ($this->class_roots as $dir) {

                $file = $dir."/".$path.".php";          //try path  "a/b.php" for class ABC
                if (!isset($this->loaded[$file]) && file_exists($file)) {
                    include $file;
		    $this->loaded[$file] = 1;
                    if (class_exists($name) || interface_exists($name)) {
                        return ;
                    }
                }

                $file = $dir."/".$path."/".$classfile.".php";           //try path  "a/b/c.php" for class ABC
                if (!isset($this->loaded[$file]) && file_exists($file)) {
                    include $file;
		    $this->loaded[$file] = 1;
                    if (class_exists($name) || interface_exists($name)) {
                        return ;
                    }
                }
            }
        }
    }



    class VanStdObjLoader implements IMap{
        public $ce = null;

        public function __construct(){
            $this->ce = new ClassExplainer();
        }

        /*fetch io class from the "path"
         *
         *@param    $path, see formate in ClassExplainer::fetch();
           */
        public function map($path){
            $class_arr = $this->ce->explain($path);
            if ($class_arr == false) {
                throw new Exception($this->ce->err, CORE_ERR_IO_ROUTING);
            } else {
                return $this->_fetch($class_arr);
            }
        }
        private function _fetch($class_arr){
            $classname = $class_arr['CLASS'];
            if (!class_exists($classname, true)){
                throw new Exception ("faild to load ioclass ,class=".$classname, CORE_ERR_IO_ROUTING);
            }

            $params = array();
            foreach ($class_arr['ARGS'] as $i => $c) {
                if (is_array($c)) {
                    $params[$i] = $this->_fetch($c);
                } else {
                    $params[$i] = trim($c);
                }
            }
            return new $classname($params);
        }
    }

    /*pares an string into an array
     *
     *  string like " a(1, b(1,2), c()) " into  $obj = new a(1, new b(1,2), new c());
     */
    class VanStdClassExplainer{
        private $_tmp_stack = array();
        private $pos = 0;
        private $str_len = null;
        public $open_tag = "(";
        public $close_tag = ")"; 
        public $arg_split = ",";
        public $err = '';

        private function _clean(){
            $this->_tmp_stack = array();
            $this->pos = 0;
            $this->str_len = null;
            $this->err = '';
        }

        public function explain($str){
            if ($this->str_len === null) {
                $this->str_len = strlen($str);
            }
            $class = '';

            // find class name
            for ( ; 
                 $this->pos < $this->str_len && $str[$this->pos] != $this->open_tag ;
                 $this->pos++ ){

                $class .= $str[ $this->pos ];
            }
            $class = trim($class);
            if (!preg_match('/^[a-zA-Z]\w*$/', $class)) {
                $this->err = "faile while fetching obj, class name wrong on str[".$this->pos."] ,class=".$class.",pos=[".$this->pos."]";
                return false;
            }
            array_push($this->_tmp_stack, $class);


            // find args
            $res = array("CLASS"=>$class, "ARGS"=>array());
            $arg = '';
            for ( $j = 0, $this->pos ++ ;  $this->pos < $this->str_len; $this->pos ++ ) {

                $arg .= $str[$this->pos];

                if ($str[$this->pos] == $this->arg_split){       //normal args like  "1, 2, awen"
                    $arg = trim($arg, $this->arg_split);
                    if ($arg) {
                        $res["ARGS"][$j++] = $arg;
                    }
                    $arg = '';

                } else if ($str[$this->pos] == $this->open_tag) { //find another class 
                    $arg = trim($arg, $this->open_tag);
                    if (!preg_match('/^[a-zA-Z]\w*$/', trim($arg))) {
                        $this->err = "wrong class name,class=".trim($arg).",pos=[".$this->pos."]";
                        return false;
                    }

                    $this->pos -= strlen($arg);
                    
                    if (!($arg = $this->explain($str))) {
                        return false;
                    }
                    if (($top_class = array_pop($this->_tmp_stack)) != $class) {    //check if it's match
                        return false;
                    } else {
                        array_push($this->_tmp_stack, $class);
                    }
                    
                    $res["ARGS"][$j++] = $arg;
                    $arg = '';

                } else if ($str[$this->pos] == $this->close_tag) {    //match the closing tag

                    array_pop($this->_tmp_stack);
                    if ($arg = trim($arg, $this->close_tag)) {
                        $res["ARGS"][$j++] = trim($arg);
                    }
                    
                    if (empty($this->_tmp_stack)) {
                        $this->_clean();
                    }
                    return $res;
                }
            }
            $this->err = "failed to metch the closing tag,class=".$class.",pos=[".$this->pos."]";
            return false;

        }

    }
