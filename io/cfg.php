<?php
	include PEPPER_ROOT . '/3party/config_center/client/php/cc_reader_manager.php';
	class IoCfg implements IIo{
		public $pri = null;
		public $aux = null;

		public function read($index=null){
			frame_assert($this->pri != null);
			frame_assert($this->aux != null);
			return CC_Reader_Manager::get($index, $this->pri, $this->aux);
		}
		
		public function write($data){}
		public function flush(){}
        public function pop(){}

	}
?>
