<?php
namespace MoreClassChildren;

class MoreClassChildren {
    private static $architect = [];
    private static $trace = false;
    private static $config = [];
    function __construct(...$class){
        foreach ($class as $instance){
            $refClass = &$instance;
            $listMethods = get_class_methods($instance);
            array_filter($listMethods,function ($m){
                return strpos($m,'_') !== 0;
            });
            self::$architect = array_merge(self::$architect,array_fill_keys($listMethods,$refClass));
        }
    }

    function __call($name, $arguments)
    {
        if(!empty(self::$architect[$name])){
            $refClass = self::$architect[$name];
            if(self::$trace){
                $this->sendTrace($name,$refClass,$arguments);
            }
            return $refClass->$name(...$arguments);
        }
    }

    function sendTrace($name,$refClass,$arguments){
        var_dump(json_encode(array_filter(
            [
                'commande' => $name,
                'class' => get_class($refClass),
                'args' => $arguments
            ]
        )));
    }

    static function setTrace(bool $trace){
        self::$trace = $trace;
    }


    static function getConfig($name){
        return self::$config[$name] ?? null;
    }
    static function setConfig($name,$value){
        self::$config[$name] = $value;
    }
    static function getInstance($class){
        $newArray = array_filter(self::$architect,function ($refClass) use($class){
            return $refClass instanceof $class;
        });
        $newArray = current($newArray);
        return $newArray;
    }

}