<?php

	/* a key=>val  solution
	 *  
	 * data struct like this:  id => json-tree ....
	 */
	$env = array(
		"db0"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db1"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db2"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db3"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),

		"cache0"=>array("CLASS"=>"std\Memcache", "host"=>"127.0.0.1", "port"=>10001),
		"cache1"=>array("CLASS"=>"std\Memcache", "host"=>"127.0.0.1", "port"=>10002),
		"cache2"=>array("CLASS"=>"std\Memcache", "host"=>"127.0.0.1", "port"=>10003),
		"cache3"=>array("CLASS"=>"std\Memcache", "host"=>"127.0.0.1", "port"=>10004),

		"role_info"=>
		array(
			"db" => array("CLASS"=>"std\MpiDb", "table"=>"m_info", "db"=>"db_info", "t_route"=>"hex(3)", "conn"=>"db0,db1,+db2"),
			"cache"=>array("CLASS"=>"std\MpiMemcached", "conn"=>"cache0,cache1,+cache2, +cache3"),
		)
	);

		
	echo "implements all of these io , and it's enough!!!"



		/*
		 * simple db multi source 
		 */


	$env = array(
		"db0"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db1"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db2"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db3"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),


		"user_db"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),

		"user_db"=>array(
			"CLASS"=>"std\MulMysql", 
			"route"=>array(
				array("select count(*) from user where id=?", "id"),
				array("update user set score=100 where id=?", "id")
			),
			"list" => array("db0", "db1", "db2", "db3")
		)

	)

	$env->io["user_db"]->query("select count(*) from user");				//execute on every db
	$env->io["user_db"]->query("select count(*) from user where score>=1000");		//execute on every db
	$env->io["user_db"]->query("select count(*) from user where score>=?", array(100));	//execute on every db
	$env->io["user_db"]->query("select count(*) from user where id=?", array(100));		//execute on the specified db
	$env->io["user_db"]->query("update user set score=100 where id=?", array(100));		//execute on the specified db


	

	$env = array(
		"db0"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db1"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db2"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),
		"db3"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),


		"user_db"=>array("CLASS"=>"std\Mysql", "host"=>"127.0.0.1", "port"=>3306, "user"=>"foo", "passwd"=>"foo"),

		"user_db"=>array(
			"read" => 0.9,
			"write" => 0.1,
			"list" => array(
				array("db0", 80),
				array("db1", 70),
				array("db2", 90),
				array("db3", 90),
			)
		)
	)



	$env->io["user_db"]->query("select count(*) from user");				//execute on one db
	$env->io["user_db"]->query("select count(*) from user where score>=1000");		//execute on one db
	$env->io["user_db"]->query("select count(*) from user where score>=?", array(100));	//execute on one db
	$env->io["user_db"]->query("select count(*) from user where id=?", array(100));		//execute on one db
	$env->io["user_db"]->query("update user set score=100 where id=?", array(100));		//execute on one db



