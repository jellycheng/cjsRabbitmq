<?php namespace CjsRabbitmq\Factory;

use CjsRabbitmq\Producer as RabbitmqProducer;

/**
 * rabbitmq消息生产列类,单例模式
 * 如果队列不可用，也要保证主业务不受影响，因此本类绝对不能抛异常
 */
class MqProducerFactory extends BaseFactory
{

    //获取生产者对象
    public function getProducer($sGroup) {
        if(isset(static::$producerMqObj[$sGroup])) {
            return static::$producerMqObj[$sGroup];
        }
        if (!isset(static::$aMQConfig[$sGroup])) {
            //fail，确保配置文件不存在也不出问题
            self::wirteError(__FUNCTION__, ['code' =>9999, 'msg' => sprintf('rabbitmq配置项%s不存在', $sGroup), 'params' =>['group'=>$sGroup]]);
            return new EmptyObj();
        } else {
            $aConfig = static::$aMQConfig[$sGroup];
            try {
                static::$producerMqObj[$sGroup] = new RabbitmqProducer($aConfig['connection'], $aConfig['exchange'], $aConfig['queue'], $aConfig['routing']);
            } catch (\Exception $e) {
                self::wirteError(__FUNCTION__, ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'params' =>['group'=>$sGroup]]);
                return new EmptyObj();
            }
        }
        return static::$producerMqObj[$sGroup];
    }


    /**
     * 验证mq是否正常
     * @param $sGroup
     * @return bool
     */
    public static function isPing($sGroup)
    {
        $obj = static::getInstance()->getProducer($sGroup);
        try {
            if ($obj->ping()) {
                return true;
            }
        } catch (\Exception $e) {
            self::wirteError(__FUNCTION__, ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'params' => ['group'=>$sGroup]]);
        }
        return false;
    }

    /**
     * mq 生产队列
     * @param $content
     * @return mixed
     */
    public static function pushWithRetry($sGroup, $content)
    {
        $obj = static::getInstance()->getProducer($sGroup);
        try {
            return $obj->PushWithRetry($content, 3000);
        } catch (\Exception $e) {
            self::wirteError(__FUNCTION__, ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'params' => ['group'=>$sGroup, 'content'=>$content]]);
        }
        return false;
    }

    /**
     * mq 生产队列 - 这个一定能发送成功，但有可能有重复发送数据的几率
     * @param $content
     * @return mixed
     */
    public static function txPushWithRetry($sGroup, $content)
    {
        $obj = static::getInstance()->getProducer($sGroup);
        try {
            return $obj->txPushWithRetry($content, 3000);
        } catch (\Exception $e) {
            self::wirteError(__FUNCTION__, ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'params' => ['group'=>$sGroup, 'content'=>$content]]);
        }
        return false;
    }

}
