<?php
    class MapXml2Csv{
        private $csvarr = array();
        public function map($xml){
            $xml = new SimpleXmlElement($xml);
            $this->_parse_xml($xml, null);

            $csv = array();
            $line = 0;
            $csv[] = array_keys($this->csvarr); /* csv head */

            $max_column = 0;
            foreach ($this->csvarr as $column) {
                $column = count($column);
                $max_column = $column > $max_column ? $column : $max_column;
            }

            for ($i = 1; $i <= $max_column; $i++) {
                foreach ($csv[0] as $c => $key) {
                    $csv[$i][] = @$this->csvarr[$key][$i - 1];
                }
            }
            return $csv;
        }

        private function _put_val($key, $val) {
            if (!isset($this->csvarr[$key])) {
                $this->csvarr[$key] = array();
            }
            $this->csvarr[$key][] = $val;
        }
        
        public function _parse_xml(SimpleXmlElement $xml, $root){
            foreach ($xml->attributes() as $key=>$val) {
                $key = ($root == null) ? $key : $root . ":" . $key;
                $this->_put_val( $key, $val->__toString());
            }

            if ($xml->count() == 0) {
                $this->_put_val($root, $xml->__toString());

            } else {
                foreach ($xml as $key => $val) {
                    $key = ($root == null) ? $key : $root . "." . $key;
                    $this->_parse_xml($val, $key);
                }
            }
        }
    }

//    class MapCsv2Xml{
//        private $csvarr = array();
//        public function map(array $csv){
//            if (@is_array($csv[0])) {
//                $key_seq = array();
//                foreach ($csv[0] as $key) {
//                    $this->csvarr[$key] = array();
//                    $key_seq[] = $key;
//                }
//
//                $row = count($csv);
//                for ($i = 1; $i < $row; $i ++) {
//                    $col = count($csv[$i]);
//                    for ($j = 0; $j < $col; $j ++) {
//                        if (isset($key_seq[$j])) {
//                            $this->csvarr[$key_seq[$j]][$i - 1] = @$csv[$i][$j];
//                        }
//                    }
//                }
//
//                $xml = new SimpleXmlElement("<root></root>");
//
//                foreach ($this->csvarr as $node => $elements) {
//                    foreach ($elements as $str) {
//                        $this->addElement($xml, $node, $str);
//                    }
//                }
//
//                return $this->csvarr;
//            }
//        }
//
//        public function addElement($xml, $node , $data) {
//            $node = explode(".", $node);
//            if (!empty($node)) {
//                $n = array_shift($node);
//                while ( !empty($node) ) {
//                    $n .= "/".array_shift();
//                }
//            }
//        }
//    }


    //$map = new MapXml2Csv();
    //print_r($map->map(file_get_contents("etc/game.xml")));
    //$csv = $map->map(file_get_contents("etc/game.xml"));
    //print_r($csv);

/************************/

require "/home/awenzhang/projects/3party/Smarty/Smarty.class.php";
    $csv = array(
            /* 1. name, 2. age, 3. price */
            array( "awen", 20,3 )
            array( "dad",30,6 )
            );
    $smarty = new Smarty();
    $smarty->asign("csv", $csv);
    $smarty->fetch("etc/game.tpl");
