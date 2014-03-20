<?php
	/*
		see opensrc()		(output@web_json)
	   */
	class IoWebJson implements IIo{
		private $doc = array();
		private $is_dirty = false;
		public final function write($data){
			core_assert(is_array($data), $data);
			foreach ($data as $key=>$val){
				$this->doc[$key] = $val;
			}
			$this->is_dirty = true;
		}

		public function read($key=null){
			if ($key !== null){
				switch (true) {
					case isset($_GET[$key]) :
						return $_GET[$key];

					case isset($_POST[$key]) :
						return $_POST[$key];

					case isset($_COOKIE[$key]) :
						return $_COOKIE[$key];

					case isset($_SESSION[$key]) :
						return $_SESSION[$key];

					case isset($_SERVER[$key]) :
						return $_SERVER[$key];

					case isset($this->doc[$key]) :
						return $this->doc[$key];

					default:
						return false;
				}
			} else {
				//get all inputs
				$all = array();
				$all = array_merge($_GET, $all);
				$all = array_merge($_POST, $all);
				$all = array_merge($_COOKIE, $all);
				$all = array_merge($_SESSION, $all);
				$all = array_merge($_SERVER, $all);
				$all = array_merge($this->doc, $all);
				return $all;
			}
		}

		public final function flush(){
			if ($this->is_dirty) {
                /* 
                  there is a bug here we didn't solved, it's warning info is :

                  "<b>Warning</b>:  json_encode() [<a href='function.json-encode'>function.json-encode</a>]: recursion detected in <b>/home/awenzhang/projects/pepper/src/core/io/core/webjson.php</b> on line <b>51</b><br />
                  <br />"

                  we have no choice but cover it */
                /*echo json_encode($this->doc);*/
				echo @json_encode($this->doc);
				$this->is_dirty = false;
				$this->doc = array();
			}
		}

        public function pop(){
            $tmp = $this->doc;
            $this->doc = array();
            return $tmp;
        }
	}
?>
