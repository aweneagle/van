<?php
namespace std;

class Get extends PregFilter {
    public function __construct(){
        $this->set_input($_GET);
    }
}
