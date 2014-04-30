<?php

    interface IMysql {
        public function query($query);
        public function connect();
        public function close();
        public function set_option($key, $val);
    }

    class Mysql implements IMysql{
    }
