<?php

    function van_add_arr(&$arr, $key, $val){
        $key = array_filter(explode('.', $key));
        if (empty($key)) {
            $arr = $val;
            return true;
        }
        $tmp = &$arr;
        foreach ($key as $i){

            if (isset($tmp[$i]) && is_array($tmp[$i])){
                $old_val = $tmp[$i];
                $tmp = &$tmp[$i];

            }else{
                $tmp[$i] = array();
                $tmp = &$tmp[$i];

                $old_val = null;
            }
        }
        if (is_array($tmp)) {
            $tmp[] = $val;
            return true;
        } else {
            return false;
        }
    }

    function van_delete_arr(&$arr, $key){
        $key = array_filter(explode('.', $key));
        if (empty($key)){
            $tmp = $arr;
            $arr = array();
            $old_val = $tmp;
            return true;
        }

        $leaf = array_pop($key);
        $tmp = &$arr;
        foreach ($key as $i){
            if (isset($tmp[$i]) && is_array($tmp[$i])){
                $tmp = &$tmp[$i];
            } else {
                return false;
            }
        }

        if (isset($tmp[$leaf])){
            $old_val = $tmp[$leaf];
            unset($tmp[$leaf]);
            return true;
        }else{
            return false;
        }

    }

    function van_write_arr(&$arr, $key, $val){
        $key = array_filter(explode('.', $key));
        if (empty($key)) {
            $arr = $val;
            return true;
        }
        $tmp = &$arr;
        foreach ($key as $i){

            if (isset($tmp[$i]) && is_array($tmp[$i])){
                $old_val = $tmp[$i];
                $tmp = &$tmp[$i];

            }else{
                $tmp[$i] = array();
                $tmp = &$tmp[$i];

                $old_val = null;
            }
        }
        $tmp = $val;
        return true;
    }

    function van_read_arr($arr, $key){
        $key = array_filter(explode('.', $key));
        $tmp = $arr;
        foreach ($key as $i){
            if (isset($tmp[$i])){
                $tmp = $tmp[$i];
            }else{
                return null;
            }
        }
        return $tmp;
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
