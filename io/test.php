<?php
/*
    set_error_handler(function($errno, $errmsg, $errfile, $errline){
        switch ($errno) {
            case E_USER_WARNING:
                echo "WARNING\n";
                    break;
            case E_USER_NOTICE:
                echo "NOTICE\n";
                    break;

            case E_USER_ERROR:
                echo "ERROR \n";
                    break;
                
            default :
                echo "UNKNOWN\n";
                    break;
        }
    }, E_ALL);
    
    trigger_error("something error", E_USER_NOTICE);
    trigger_error("something error", E_USER_WARNING);
    trigger_error("something error", E_USER_ERROR);
    die("here");
    
*/

class B{
    public function __construct(array &$a = null){
        if ($a === null) {
           echo "a\n";
        } else {
            echo "A\n";
        }
    }
}
$b = new B;
$a = array();
new B($a);
die;

    class A{
    //    private $a;
        private $b;
        public function __isset($name){
            return property_exists($this, $name);
        }
        public function __set($name, $val){
            $this->$name = $val;
        }

        public function __get($name){
            return $this->$name;
        }
    }

    $a = new A;
    var_dump(isset($a->a));
  //  $a['a'] = "1";
    $a->a = 1;

