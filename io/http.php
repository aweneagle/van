<?php
    
    /* io interfaces */

    interface IIterator extends Iterator{}

    interface IQuery {
        public function query($query, array $params);
    }

    interface ICtrl{
        public function open();
        public function close();
        public function set_attr($key, $val);
    }

    interface IBuffer{
        public function flush();
        public function clean();
    }

    interface IStack {
        public function push();
        public function pop();
    }

    interface IHash{
        public function get($key, $op=array());
        public function set($key, $val, $op=array());
        public function del($key, $op=array());
    }

    interface IJob{
        public function run();
    }

    class IoMysql implements IQuery, ICtrl{}

    class IoMemcache implements IHash, ICtrl{}

    class IoHttp implements IQuery, ICtrl{}

    class IoLogFile implements IStack, ICtrl{}

    class IoLogSocket implements IStack, ICtrl{}

    class IoCsv implements IIterator, ICtrl{}

    class IoShm implements IHash, ICtrl{}


    class IoWeb implements IHash, IBuffer{}

    class IoMpi implements IHash, IBuffer{}

    class IoLog implements IStack{}

    class IoCfg implements IHash{}

    class IoApi implements IQuery{}



    mo_link($link, $obj|$link);
    mo_unlink($link);
    mo_get($link)->open();

    $mo = new Mo();
    $mo->link($link, $obj|$link);
    $mo->unlink($link);
    $mo->get($link)->open();

    $mo = Mo::init();

    /* init env */
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    $mo->link($link, $obj|$link);
    ...


    /* uninit env */
    $mo->unlink($link);
    $mo->unlink($link);
    $mo->unlink($link);
    $mo->unlink($link);
    $mo->unlink($link);
    ...


    /* business */
    $mo->get_ctrl($link)->open();
    $mo->get_buffer($link)->flush();
    
    $mo->get($link)->open();
    $mo->get($link)->flush();

    ?>
