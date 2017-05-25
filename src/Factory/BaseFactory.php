<?php
namespace CjsRabbitmq\Factory;

abstract class BaseFactory {

    protected static $producerMqObj = []; //rabbitmq生产者实例
    protected static $consumerMqObj = []; //rabbitmq消费者实例
    protected static $aMQConfig     = []; //rabbitmq配置

    protected static $logObj;

    protected function __construct(){}

    public static function getInstance() {
        static $me;
        if($me) {
            return $me;
        }
        $me = new static;
        return $me;
    }

    public function setConfig($mqConfig) {
        static::$aMQConfig = array_merge(static::$aMQConfig, (array)$mqConfig);
        return $this;
    }

    public function setGroupConfig($group, $config) {
        static::$aMQConfig[$group] = (array)$config;
        return $this;
    }

    /**
     * 获取rabbitmq配置
     *
     * @return array
     */
    public function getConfig()
    {
        return self::$aMQConfig;
    }

    public function setLogObj($logObj) {
        self::$logObj = $logObj;
        return $this;
    }

    public function getLogObj() {
        return self::$logObj;
    }
    
    protected static function wirteError($functionName, $errorData = []) {
        if(self::$logObj && method_exists(self::$logObj, 'handle')) {
            call_user_func_array([self::$logObj, 'handle'], ['functionName'=>$functionName, 'errorData'=>$errorData]);
        }
    } 
    
}