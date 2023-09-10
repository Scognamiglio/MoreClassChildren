<?php
namespace MoreClassChildren;

class MoreClassChildren {
    private static $architect = [];
    private static $trace = false;
    private static $config = [];
    private $id = 0;
    private static $lastId = 0;
    function __construct(...$class){
        $this->id = self::$lastId;
        foreach ($class as $instance){
            $refClass = &$instance;
            $listMethods = get_class_methods($instance);
            array_filter($listMethods,function ($m){
                return strpos($m,'_') !== 0;
            });
            self::$architect[$this->id] = array_merge(self::$architect[$this->id] ?? [],array_fill_keys($listMethods,$refClass));
        }
        self::$lastId++;
    }

    function __call($name, $arguments)
    {
        if(!empty(self::$architect[$this->id][$name])){
            $refClass = self::$architect[$this->id][$name];
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

    function getConfigInstance($name){
        return self::$config[$this->id][$name] ?? self::$config[$name] ?? null;
    }
    function setConfigInstance($name,$value){
        self::$config[$this->id][$name] = $value;
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