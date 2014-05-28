<?php
namespace std;
interface ICtrl {
    public function __get($name);
    public function __set($name, $value);
    public function __isset($name);
}
class PhpInput extends PregMatch {
    private $input;

    private $format = null;

    public function __isset($name){
        switch ($name) {
            case 'format':
                return true;
        }
        return false;
    }

    public function __get($name){
        switch ($name) {
            case 'format':
                return $this->$name;
        }
        return false;
    }

    public function __set($name, $value){
        switch ($name) {
            case 'format':
                $this->set_format($value);
                break;
    
            default:
                return false;
        }
        return $this->$name;
    }

    public function __construct($format=null){
        $this->set_format($format);
    }
    
    private function set_format($format=null){
        switch (strtolower($format)) {
            case 'json':
                $this->input = @json_decode(file_get_contents("php://input"));
                break;

            case 'xml':
                $this->input = @json_decode(@json_encode(@simplexml_load_file("php://input")), true);
                break;

            case null:
                $this->input = array();
                break ;

            default :
                $this->input = array();
                trigger_error("unknown php-input format, format=".$format, E_USER_ERROR);
                break ;
        }
        if (!is_array($this->input)) {
            trigger_error("wrong php-input format, it's not json, error=".error_get_last(), E_USER_ERROR);
            $this->input = array();
        }
        $this->set_input($this->input);
    }

}
