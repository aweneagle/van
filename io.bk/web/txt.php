<?php
	class IoWebTxt implements IIo{

		private $output = null;
		private $is_dirty = false;

		public function read($key=null){
			if ($key !== null){
				switch (true) {
					case isset($_GET[$key]):
						return $_GET[$key];

					case isset($_POST[$key]): 
						return $_POST[$key];

					case isset($_COOKIE[$key]): 
						return $_COOKIE[$key];

					case isset($_SESSION) && isset($_SESSION[$key]): 
						return $_SESSION[$key];

                    case isset($_SERVER[$key]):
                        return $_SERVER[$key];

					default:
						return false;
				}

			} else {
				//get all inputs
				$all = array();
				$all = array_merge($_GET, $all);
				$all = array_merge($_POST, $all);
				$all = array_merge($_COOKIE, $all);
                $all = isset($_SERVER) ? array_merge($_SERVER, $all) : $all;
				$all = isset($_SESSION) ? array_merge($_SESSION, $all) : $all;
				return $all;
			}
		}

		public final function write($data){
			if (!is_array($data)){
				$this->output = $data;
			}else{
				if ($this->output == null){
					$this->output = array();
				}
				$this->output = array_merge($this->output, $data);
			}
			$this->is_dirty = true;
		}

        public function pop(){
            $tmp = $this->output;
            $this->output = array();
            return $tmp;
        }

		public final function flush(){	
			if ($this->is_dirty) {
				if (is_array($this->output)){
					echo json_encode($this->output);
				}else{
					echo $this->output;
				}
				$this->is_dirty = false;
				$this->output = array();
			}
		}
	}
?>
