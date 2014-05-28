<?php
/*
 *   2014.05.23
 * 
 * it provides two functions , input and output 
 *
 * input function :
 *      a combination of POST , GET, REQUEST, and php://input 
 *
 * output function 
 *      1. format:   'json', 'html', and 'xml' is available
 *      2. when using 'html', smarty is required, and the tpl path should be set( Web::tpl )
 *
 */

namespace std;

class Web implements IBuffer, IPreg, ICtrl{
    const NOT_FULL_MATCH = false;
    const FULL_MATCH = true;
    private $post = new std\Post;
    private $get = new std\Get;
    private $request = new std\Request;
    private $php_inpt = new std\PhpInput;

    private $buffer = new std\Preg;

    private $format = 'json';   /* 'json', 'xml', and 'html' is acceptable */
    private $tpl_path = '';     /* tpl , used by smarty when formt == 'html' */
    private $smarty = new Smarty;   /* smarty , when format == 'html' */
    private $xml = new std\Xml; /* xml,  when format == 'xml' */

    private function register_tpl_path($tpl_path){
        $this->tpl_path = $tpl_path;
    }

    private function register_format($format){
        $format = strtolower($format);
        if ($format!="json" && $format!="xml" && $format!="html") {
            trigger_error("wrong format, format=".$format, E_USER_WARNING);
            return ;
        }
        $this->format = $format;
    }

    public function __isset($name){
        return property_exists($this, $name);
    }

    public function __get($name){
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
                trigger_error("unknown property name for Web,name=".$name, E_USER_WARNING);
                return false;
        }
    }

    public function __set($name, $value){
        switch ($key) {
            case 'format':
            case 'tpl_path':
                $method = 'register_'.$key;
                $this->$method($val);
                break;

            default :
                trigger_error("unknown config for Web, conf=".$key, E_USER_ERROR);
                break;
        }
    }

    public function get($key, $preg=null, $fullmatch=IPreg::NOT_FULL_MATCH){
        $value = null;
        switch (true) {
            case $this->buffer->exists($key) :  $value = $this->buffer->get($key, $preg, $fullmatch); break;
            case $this->post->exists($key) :  $value = $this->post->get($key, $preg, $fullmatch); break;
            case $this->get->exists($key) :  $value = $this->get->get($key, $preg, $fullmatch); break;
            case $this->request->exists($key) :  $value = $this->request->get($key, $preg, $fullmatch); break;
            case $this->php_input->exists($key) :  $value = $this->php_input->get($key, $preg, $fullmatch); break;
            default:
                return false;
        }
        return $value;
    }

    public function set($key, $val, $preg=null){
        return $this->buffer->set($key, $val, $preg);
    }

    public function all(){
        return array_merge($this->buffer->all(), $this->post->all(), $this->get->all(), $this->request->all(), $this->php_input->all());
    }

    public function exists($key){
        return $this->buffers->exists($key) || $this->post->exists($key) || $this->get->exists($key) || $this->request->exists($key) || $this->php_input->exists($key);
    }

    public function flush(){
        switch ($this->format){
            case 'json':
                echo json_encode($this->buffer->all());
                break;
            case 'xml':
                echo $this->xml->asStr($this->buffer->all());
                break;
            case 'html':
                $this->smarty->display($this->tpl_path, $this->buffer->all());
                break;

            default:
                trigger_error("unknown output format, format=".$this->format);
                break;
        }
        return true;
    }

    public function clean(){
        $output = $this->buffer->all();
        $this->buffer = new std\Preg();
        return $output;
    }
}
