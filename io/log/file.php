<?php


	/* log modules */
	class LogFile implements IStream extends LogBase{
		private $path = "/data/debug";
		private $split = "|";
		public function read(){}
		public function write($line){
			$fp = @fopen($this->path, "a");
			if (!$fp) {
				throw new Exception("failed to open file,file=".$this->path.",err=".@json_encode(error_get_last()));
			}
			if (fwrite($fp, $line) === false){
				throw new Exception("failed to write file,file=".$this->path.",err=".@json_encode(error_get_last()));
			}
			if (fclose($fp) === false) {
				throw new Exception("failed to close file,file=".$this->path.",err=".@json_encode(error_get_last()));
			}
			return true;
		}
		public function log(){ return $this->write(implode("|", func_get_args()));}
	}
