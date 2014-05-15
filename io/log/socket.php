<?php

	class LogSocket implements IStream {
		private $path = "socket:/var/someone.socket";
		public function read(){}
		public function write($line){}
		public function log(){}
	}
