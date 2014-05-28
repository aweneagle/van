<?php
namespace std;

class Preg implements  IPreg {
    private $input = array();

    public function set_input(array &$input = null){
        $this->input = $input;
    }

    /* get values by preg match 
     *
     * @param   $preg   pattern string, 
     * @param   $match      IPreg::NOT_FULLY_MATCH | IPreg::FULLY_MATCH
     *          when $match == IPreg::FULLY_MATCH, the value of $preg will be used directly to compare the value to fetch
     */
	public function get($key, $preg=null, $match=IPreg::NOT_FULLY_MATCH){
        if (!isset($this->input[$key])) {
            return null;
        }

        $value = $this->input[$key];
        if ($match == IPreg::NOT_FULLY_MATCH) {  
            if ($preg == null) {
                return $value;
            }

            if (preg_match($preg, $value)) {
                return $value;
            } else {
                return null;
            }

        } else {
            if ($preg == $value) {
                return $value;
            } else {
                return null;
            }
        }
    }

    /* set values by preg match
     *
     * @param   $preg   pattern, when it's provided, only values matched can be set succefully
     */
	public function set($key, $val, $preg=null){
        if ($preg != null && !preg_match($preg, $val)) {
            return false;
        }
        $this->input[$key] = $val;
        return true;
    }

	public function exists($key){
        return isset($this->input[$key]);
    }

    public function all(){
        return $this->input;
    }
}
