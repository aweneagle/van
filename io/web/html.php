<?php
	define ('WEB_SMARTY_COMPILE_DIR', "/data/pepper/smarty/compile");
	define ('WEB_SMARTY_CACHE_DIR', "/data/pepper/smarty/cache");
	include PEPPER_ROOT . '/3party/smarty/Smarty.class.php';
	class IoWebHtml implements IIo, ICtrl{
		private $doc = array();
		public $tpl = null;
		private $is_dirty = false;
		public $smarty_compile_dir = WEB_SMARTY_COMPILE_DIR;
		public $smarty_cache_dir = WEB_SMARTY_CACHE_DIR;
		public $smarty_left_delimiter = '<!--{';
		public $smarty_right_delimiter = '}-->';

		public final function write($data){
			van_assert(is_array($data), "html doc must be array",$data);
			foreach ($data as $key=>$val){
				$this->doc[$key] = $val;
			}
			$this->is_dirty = true;
		}

		public final function read($index=null){
		}
		
		public final function setTemplate($tpl){
			$this->tpl = $tpl;
		}

        public function pop(){
            $tmp = $this->doc;
            $this->doc = array();
            return $tmp;
        }

		public final function flush(){
			if ($this->is_dirty) {
				van_assert(file_exists($this->tpl), "file do not exists", $this->tpl);
				$smarty = new Smarty();
				$smarty->caching = false;
				$smarty->left_delimiter = $this->smarty_left_delimiter;
				$smarty->right_delimiter = $this->smarty_right_delimiter;
				$smarty->setCompileDir($this->smarty_compile_dir);
				$smarty->setCacheDir($this->smarty_cache_dir);

                /*
				foreach ($this->doc as $key => $val){
					$smarty->assign($key, $val);
				}
                */
                $smarty->assign('doc', $this->doc);

				$this->is_dirty = false;
				$this->doc = array();
				echo $smarty->fetch($this->tpl);
			}
		}

	}
?>
