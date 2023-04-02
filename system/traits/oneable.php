<?php

namespace icms\traits;

trait oneable {

    public function getOnce($context = null) {

        if($context === null){
            $context = $this;
        }

        return new class($context) {

            private static $cached = [];

            private $obj;

            public function __construct($obj) {
                $this->obj = $obj;
            }

            public function __call($name, $arguments = []) {

                $call_hash = md5($name . serialize($arguments));

                if(!array_key_exists($call_hash, self::$cached)){

                    self::$cached[$call_hash] = call_user_func_array([$this->obj, $name], $arguments);
                }

                return self::$cached[$call_hash];
            }
        };

    }

}
