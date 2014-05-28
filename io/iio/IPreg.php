<?php
namespace iio;
interface IPreg {
    const FULLY_MATCH = 1;
    const NOT_FULLY_MATCH = 0;
    public function get($key, $preg=null, $match=self::NOT_FULLY_MATCH);
    public function set($key, $preg=null);
}
