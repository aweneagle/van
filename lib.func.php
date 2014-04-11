<?php

    /*  get the node of an array
     *
     * @param $arr, mixed , string, num or array ...  
     * @param $key, the index like "a.b.c", it map to the $arr['a']['b']['c']
     * @param $op, enum, ('get'|'set'|'add'|'addnum'|'del'|'dec')
     * @param $strict, make sure that the arr is not modefied when node is not found
     */
    function van_op_arr(&$arr, $key, $op, $val=null, &$oldval=null, $strict=false){
        $key = array_filter(explode('.', $key));
        $node = &$arr;

        $p_node = null;

        foreach ($key as $i){

            if (is_array($node)){
                if (!isset($node[$i])) {
                    if ($strict == true) {
                        return false;
                    }
                    $p_node = &$node;
                    $node[$i] = array();
                    $node = &$node[$i];

                } else {
                    $p_node = &$node;
                    $oldval = $node = &$node[$i];
                }
            }else{
                if ($strict == true) {
                    return false;
                }

                $node = array();
                $p_node = &$node;
                $node[$i] = null;
                $node = &$node[$i];
            }
        }

        switch ($op) {
            case 'decnum':
                if (empty($node)) {
                    $node = 0;
                }
                if (is_numeric($node) && is_numeric($val)) {
                    $node -= $val;
                    return true;

                } else {
                    return false;
                }
                break;
            case 'addnum':
                if (empty($node)) {
                    $node = 0;
                }
                if (is_numeric($node) && is_numeric($val)) {
                    $node += $val;
                    return true;

                } else {
                    return false;
                }
                break;

            case 'add':
                if (is_array($node)) {
                    $node[] = $val;
                    return true;
                }else{
                    return false;
                }
                break;

            case 'set':
                $node = $val;
                return true;

            case 'del':
                unset($p_node[$i]);
                return true;

            case 'get':
                return $node;

            default:
                return false;
        }
    }

    function van_addnum_arr(&$arr, $key, $val, $oldval=null){
        return van_op_arr($arr, $key, 'addnum', $val, $oldval);
    }

    function van_add_arr(&$arr, $key, $val, $oldval=null){
        return van_op_arr($arr, $key, 'add', $val, $oldval);
    }

    function van_delete_arr(&$arr, $key, $oldval=null){
        return van_op_arr($arr, $key, 'del', null, $oldval, true);
    }

    function van_write_arr(&$arr, $key, $val, $oldval=null){
        return van_op_arr($arr, $key, 'set', $val, $oldval);
    }

    function van_read_arr($arr, $key, $oldval=null){
        return van_op_arr($arr, $key, 'get', null, $oldval, true);
    }

    function van_decnum_arr(&$arr, $key, $val, $oldval=null){
        return van_op_arr($arr, $key, 'decnum', $val, $oldval);
    }

    function van_map_class2path($classname, &$path, &$file){
        $strlen = strlen($classname);
        $path = '';
        $tmp = '';
        for ($i = 0; $i < $strlen; $i ++) {
            $curr = $classname[$i];

            if ($curr >= 'A' && $curr <= 'Z') {
                if ($tmp !== '') {
                    $path .= $tmp . "/";
                    $tmp = '';
                }

                $tmp .= lcfirst($curr);

            } else {
                $tmp .= $curr;
            }
        }
        $file = $tmp;
    }
