<?php
	class IoLog implements IIo{
		protected $filepath;
		protected $LINE_SP = "\n";
		protected $data = array();
        public $dir_mod = 0777;
		public function __construct($params){
            van_assert(isset($params["path"]), "empty file path");
			$filepath = $params["path"];
            
            /* permissions of the file */
            if (!isset($params[1])) {
                $permission = 0666;
            } else {
                $permission = intval($params[1], 8);
            }

            if (file_exists($filepath)) {
                $try_to_mod = @chmod($filepath, $permission);
                $try_to_open = fopen($filepath,"a");
                van_assert($try_to_open != null, "failed to open file");
                fclose($try_to_open);

                $this->filepath = $filepath;
            } else {
                $dir = dirname($filepath);
                if (!is_dir($dir)) {
                    van_assert( !file_exists($dir), "dir path already exists ");
                    van_assert(@mkdir($dir, $this->dir_mod), "failed to create dir");
                }
                $try_to_create = @touch($filepath);
                $try_to_mod = @chmod($filepath, $permission);
                van_assert($try_to_create != false, "failed to create file", $filepath); 
                van_assert($try_to_mod != false, "failed to chmod file"); 
                $this->filepath = $filepath;
            }
		}
		public function write($line){
			$this->data[] = $line;
		}
		public function read($index=null){}
        public function pop(){
            $tmp = $this->data;
            $this->data = array();
            return $tmp;
        }
		public function flush(){
			$fp = fopen($this->filepath, "a");
			foreach ($this->data as $line){
				$line = is_array($line) ? @json_encode($line) : $line;
				fwrite($fp, $line.$this->LINE_SP);
			}
			fclose($fp);
			$this->data = array();
		}
	}
?>
