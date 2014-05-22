<?php
class CsvCachedDb implements ICsv{
	public	$key = 'id'; 
    private $storage = new CsvMysql;
    private $cache = new CsvMemcached;
    public function fetch(array $csv){}
    public function replace(array $csv){}
    public function delete(array $csv){}
}

