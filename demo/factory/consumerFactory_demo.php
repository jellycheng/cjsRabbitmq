<?php
/**
 * 消费工厂
 */
require_once dirname(__DIR__) . '/common.php';
use CjsRabbitmq\Factory\MqConsumerFactory;

$sGroup ="test_1";
$watch = true;
MqConsumerFactory::getInstance()->setConfig(include dirname(__DIR__) . '/config/rabbitmq.php');
//消耗队列
while(true) {
    $ret = MqConsumerFactory::consumeWithRetry($sGroup, function($msg, $consume){
        echo $msg. PHP_EOL; //消息内容，解析json格式，进行队列分发及业务逻辑处理
        //消耗队列逻辑


        $consume->ack(); //固定写法
    });
    if(!$ret && !$watch) {//
        break;
    }
}

