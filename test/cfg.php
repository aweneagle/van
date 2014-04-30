
<?php 
define('WWW_DB0', 'WWW_DB0'); 
$__tmpioarr = array(
	"host" => array(
		"0" => "127.0.0.1",
		"1" => "127.0.0.1",
		"2" => "127.0.0.1"),
	"port" => "3306",
	"user" => "awen",
	"passwd" => "awen",
	"CLASS" => "IoMysql");
$__tmpioarr = array (WWW_DB0 => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

define('WWW_DB1', 'WWW_DB1'); 
$__tmpioarr = array(
	"host" => "127.0.0.1",
	"port" => "3306",
	"user" => "awen",
	"passwd" => "awen",
	"CLASS" => "IoMysql");
$__tmpioarr = array (WWW_DB1 => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

define('WWW_CACHE0', 'WWW_CACHE0'); 
$__tmpioarr = array(
	"host" => "127.0.0.1",
	"port" => "10001",
	"CLASS" => "IoMemcached");
$__tmpioarr = array (WWW_CACHE0 => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

define('WWW_CACHE1', 'WWW_CACHE1'); 
$__tmpioarr = array(
	"host" => "127.0.0.1",
	"port" => "10002",
	"CLASS" => "IoMemcached");
$__tmpioarr = array (WWW_CACHE1 => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

define('WWW_MYSQL_USER', 'WWW_MYSQL_USER'); 
$__tmpioarr = array(
	"db" => array(
		"0" => "WWW_DB0",
		"1" => "WWW_DB1",
		"2" => "WWW_DB2"),
	"dbname" => "db_pepper_user",
	"table_pref" => "m_user_",
	"CLASS" => "IoMpiMysql");
$__tmpioarr = array (WWW_MYSQL_USER => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

define('WWW_REDIS_USER', 'WWW_REDIS_USER'); 
$__tmpioarr = array(
	"storage" => array(
		"0" => "WWW_REDIS0",
		"1" => "WWW_REDIS1"),
	"CLASS" => "IoMpiRedis");
$__tmpioarr = array (WWW_REDIS_USER => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

define('WWW_CACHE_USER', 'WWW_CACHE_USER'); 
$__tmpioarr = array(
	"cache" => array(
		"0" => "WWW_CACHE0",
		"1" => "WWW_CACHE1"),
	"CLASS" => "IoMpiMemcache");
$__tmpioarr = array (WWW_CACHE_USER => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

define('WWW_USER', 'WWW_USER'); 
$__tmpioarr = array(
	"storage" => "WWW_MYSQL_USER",
	"cache" => "WWW_CACHE_USER",
	"CLASS" => "IoMpi");
$__tmpioarr = array (WWW_USER => $__tmpioarr );
van_batch_link( $__tmpioarr ); 
unset($__tmpioarr);

