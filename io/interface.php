<?php

	/* "stream-style" and "block-style" are the base io styles(excepct the "event-style" io)*/
	interface IIo{
	}

    abstract class IEvent{
        const STOP = 0x01;
        const PAUSE = 0x02;
        const RESUME = 0x03;
        public $sleep = 1;  /* seconds */
        public function set_handler($event, $func){}
        public function run($func){}
    }

	interface IStream extends IIo{
		public function read();
		public function write($data);
	}

	interface IBlock extends IIo{
		public function read($query, $params);
		public function write($query, $params);
	}

	/* io buffer */
	interface IBuffer extends IIo{
		public function flush();
		public function clean();
	}


	/* io attribute */
	interface IArr extends IIo{
		public function __get();
		public function __set();
	}


	/* io data formate */

	interface IHash extends IIo{
		public function get($key);
		public function set($key, $val);
        public function exists($key);
	}

	interface ITree extends IIo{
		public function get(array $subtrees);
		public function set(array $subtrees);
		public function del(array $subtrees);
        public function exists(array $subtrees);
	}

	interface ICsv extends IIo{
		public function fetch(array $csv);
		public function update(array $csv);
		public function delete(array $csv);
        public function count();
	}


