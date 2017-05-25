<?php
/**
 * 生产队列
 */
require_once dirname(__DIR__) . '/common.php';

use CjsRabbitmq\Factory\MqProducerFactory;


$sGroup = "test_1";
$mqProdObj = MqProducerFactory::getInstance()->setConfig(include dirname(__DIR__) . '/config/rabbitmq.php')->setLogObj(new xxxLog());
//$mqProdObj->getProducer($sGroup);
//MqProducerFactory::getInstance()->getProducer($sGroup);
//MqProducerFactory::getInstance()->getProducer($sGroup);

//var_dump(MqProducerFactory::isPing($sGroup));
//exit;
$i = 0;
$max = 100; //造多少条记录数
while($i<$max) {
    $i++;
    $contents = [
        'id'=>mt_rand(000, 999),
        'type'=>'agent',
        'show_status'=>1,
        'timestamp'=>time(),
    ];
    //放入队列
    MqProducerFactory::pushWithRetry($sGroup, json_encode($contents));
}

class xxxLog {
    public function handle($functionName, $errorData) {

        echo $functionName . PHP_EOL;
        var_export($errorData);
        echo PHP_EOL;
    }
}