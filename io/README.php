<?php

    $env = new Env();
    $env->config(array("configure"));
    $env->g_load(WEB_ROOT . '/lib/functions.php');        /* it will be shared betweem all environment instance */
    $env->g_add_class_root(WEB_ROOT . '/job');
    env_json2xml($json, $xml);
    env_xml2json($xml, $json);
    $env->query('job\BuySomeThing', array(), Env::QUERY_SAFE);


    $env = new Env(Env::EMPTY_ENV);
    $env->config(array("configure"));
    $env->g_set_err_handler(new std\ErrorHandler);        /* it will replace the current error handler , and shared betweem all environment */
    $env->web;          /* web inputs */
    $env->http;         /* curl http */
    $env->https;        /* curl https */
    $env->db0;          /* mysql */
    $env->memcached0;   /* memcached */
    $env->redis0;       /* redis */
    $env->log;          /* file */
    $env->socket;       /* socket */
    $env->shm;          /* shared memory */
    $env->cfg;          /* configuration tool based on shared memory */


    $env->web->get("name", '/^\w+$/');
    $env->web->set("name", "foo");
    $env->web->format = "html";
    $env->web->tpl = "/index.html";
    $env->web->flush();


    $env->db0->options = array();
    $env->db0->config(
        array( "host" => "127.0.0.1",  "port" => "3306" , "user" => "foo" , "passwd" => "foo", "db" => "foo")
    );
    $env->db0->query("call new_id(?)", array(1));


    $env->https->config( array("host"=>"www.google.com") );
    $env->https->options[CURLOPT_CACERT_INFO] = './pay.pem';
    $env->https->query("/search", array("keywards"=>"foo"));

    
    $env->log->path = DATA_ROOT . "/err.log";
    $env->log->spliter = "|";
    $env->log->time_format = "%Y%m%d";
    $env->log->log("a", "b", "c", "d");


    $env->shm->key = 0x00001;
    $env->shm->mod = 'r';
    $env->shm->read(1, 10);
    $env->shm->mod = 'w';
    $env->shm->write(1, 10 , "some thing should be writen in");


    $env->cfg->shm = $env->shm;
    $env->cfg->read("a.b.c");
    $env->cfg->shm->mod = 'w';
    $env->cfg->write(array("a"=>array("b"=>array("c"=>"something should be write into shared memory"))));


    $env->query('std/Memcache', 'GET', $key);

    $env->memcache0->get($key);

    $env->obj('job/Job')->run($params);
    $env->query('job/Job', $params, 'safe');

    

    /*
    $env->web->preg('/^\w+$/');
    $env->web->name;

    $env->assert(preg_match('/^\w+$/', $env->web->name) == true);
    */
    
    

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



