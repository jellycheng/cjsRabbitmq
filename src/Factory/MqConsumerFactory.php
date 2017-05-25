<?php namespace CjsRabbitmq\Factory;

use CjsRabbitmq\Consumer as RabbitmqConsumer;

/**
 * rabbitmq消费队列工厂，队列挂了也不能让主业务挂掉
 */
class MqConsumerFactory extends BaseFactory{


    public function getConsumer($sGroup)
    {
        if(isset(static::$consumerMqObj[$sGroup])) {
            return static::$consumerMqObj[$sGroup];
        }
        if (!isset(static::$aMQConfig[$sGroup])) {
            //fail，确保配置文件不存在也不出问题
            self::wirteError(__FUNCTION__, ['code' =>9999, 'msg' => sprintf('rabbitmq配置项%s不存在', $sGroup), 'params' =>['group'=>$sGroup]]);
            return new EmptyObj();
        } else {
            $aConfig = static::$aMQConfig[$sGroup];
            try {
                static::$consumerMqObj[$sGroup] = new RabbitmqConsumer($aConfig['connection'], $aConfig['exchange'], $aConfig['queue'], $aConfig['routing']);
            } catch (\Exception $e) {
                self::wirteError(__FUNCTION__, ['code' =>$e->getCode(), 'msg' => $e->getMessage(), 'params' =>['group'=>$sGroup]]);
                return new EmptyObj();
            }
        }
        return static::$consumerMqObj[$sGroup];
    }
    
    /**
     * rabbitmq 消费队列
     * @param $sGroup
     * @param $callback
     * @return mixed
     */
    public static function consumeWithRetry($sGroup, $callback)
    {
        $obj = static::getInstance()->getConsumer($sGroup);
        try {
            return  $obj->consumeWithRetry(function($msg) use ($callback, $obj){
                call_user_func($callback, $msg, $obj);
            }, 1000);
        } catch (\Exception $e) {
            self::wirteError(__FUNCTION__, ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'params' => ['group'=>$sGroup]]);
        }
        return false;
    }
    
}
