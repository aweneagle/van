<?php
namespace std;

class Request extends PregFilter{
    public function __construct(){
        $this->set_input($_REQUEST);
    }
}
