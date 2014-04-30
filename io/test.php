<?php
    interface A{
        public function a();
    }
    interface B{
        public function a($param);
    }
    interface C extends A,B{}
    class CC implements C{
        public function a(){echo "a\n";}
        public function b(){echo "b\n";}
    }

    $c = new CC;
    $c->a();
?>
