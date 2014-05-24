<?php
/* autho : awen
 *
 * 2014.05.23
 *
 * it provides two functions , input and output 
 *
 * input function :
 * 		a combination of POST , GET, REQUEST, and php://input 
 *
 * output function 
 * 		1. format:   'json', 'html', and 'xml' is available
 * 		2. when using 'html', smarty is required, and the tpl path should be set( Web::tpl )
 *
 */

namespace std;

class Web implements IHash, ICtrl{
    private $post = new std\Post;
    private $get = new std\Get;
	private $request = new std\Request;
    private $php_inpt = new std\PhpInput;

    private $buffer = array();
	private $format = 'json';	/* 'json', 'xml', and 'html' is acceptable */
	private $tpl_path = '';		/* tpl used by smarty when using 'html' format */

	/* configure the params
	 *
	 * when not-existing params is given , warning will be given, and the configuration will be ignored
	 * when wrong param value is given, exception will be thrown 
	 */
    public function config(array $conf){
		foreach ($conf as $key => $val) {
			switch ($key) {
			}
		}
    }

    public function __isset($name){
    }

    public function __get($name){
    }

    public function __set($name, $value){
    }

    public function get($key, $preg=null){
    }

    public function set($key, $val, $preg=null){
    }

    public function allkeys(){
    }

    public function exists($key){
    }
}
