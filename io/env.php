<?php
	class Env implements IArr{
		private $post;	/* $_POST */
		private $get;   /* $_GET */
		private $request; /* $_REQUEST */
		private $server; /* $_SERVER */
		private $cookie; /* $_COOKIE */
		private $session; /* $_SESSION */
		private $php_input;  /* php://input */

		private $stdin = new StdWeb;
		private $stdout;
		private $stderr;

		private $io = new StdIoList();

        public function load(array $cfg){
        }
		
		public function __get($name){
			if (property_exists($this, $name)) {
				return $this->$name;
			} else {
				throw new Exception("no property found ,class=".get_class($this).",property=".$name);
			}
		}
		public function __set($name, $val){
			if (!property_exists($this, $name)) {
				throw new Exception("no property found ,class=".get_class($this).",property=".$name);
			}

			switch ($name) {
			case 'io':
				if (! $val instanceof StdIoList) {
					throw new Exception("wrong io object, need instance of StdIoList,curr=".get_class($val));
				}
				$this->io = $val;
				break;

			case 'stdin':
			case 'stdout':
			case 'stderr':
			case 'post':
			case 'get':
			case 'request':
			case 'session':
			case 'cookie':
			case 'server':
				if (! $val instanceof IArr) {
					throw new Exception("wrong io object, it dosen't implements interface IArr,property=".$name.",class=".get_class($val));
				}
				$this->$name = $val;
				break;

			default:
				throw new Exception("unknown property, property=".$name);

			}
		}
	}

	$env = new Env;
	$env->post->reg='/^\$[0-9]+$/';
	$name = $env->post->name;
	$env->role_info->get(array($uid=>array("role_info", "role_items")));

	$env->game_cdk->fetch(array("id"=>$cdk,"state"=>null, "expired"=>null));
	$env->game_cdk->delete(array("id"=>$cdk));

	$env->actlog->log($uid, "succ", $abc, $efg);

	foreach ($env->item_csv as $line) {
		echo $line['name'] . "|" . $line['uid'] ;
	}

    $env->eh->set_handler(
        IEvent::STOP,
        function(){
            Env::$actlog->log("job finished");
        }
    );
    $env->eh->run(
        function(){
            Env::load(
                array(
                    "role_info" => array (
                        "host" => "127.0.0.1",
                        "port" => "3306",
                        "user" => "foo",
                        "passwd" => "foo",
                        "db" => "db_role",
                        "table" => "m_role",
                        "table_router" => "hex(2)"
                        )
                    ),
                array(
                    "role_info_cache" => array (
                        "host" => "127.0.0.1",
                        "port" => "3306",
                        )
                    )
                );

            $allkeys = Env::$role_info_cache->allkeys();
            foreach ($allkeys as $k) {
                $val = Env::$role_info_cache->fetch(array("id"=>$k, "data"=>null));
                Env::$role_info->update($val);
            }
        }
    );











