<?php 
    class MapCsv2Arr implements IMap {
        public $splt = ',';
        public function map($csv_str){
             $arr = array();
             $lines = explode("\n", $csv_str);
             foreach ($lines as $l) {
                  $arr[] = explode($this->splt, $l);
             }
             return $arr;
        }
    }
