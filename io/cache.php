<?php
    interface ICache {
        public function connect();
        public function get($key);
        public function set($key, $val, $compress, $expired);
        public function close();
    }

    class IoCacheMem implements ICache {
    }

    class IoCacheRedis implements ICache{
    }
