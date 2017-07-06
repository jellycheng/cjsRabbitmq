<?php
/**
 * 消费广播
 */
require_once dirname(__DIR__) . '/common.php';

use CjsRabbitmq\Consumer as RabbitmqConsumer;
$consumer = new RabbitmqConsumer([
    'host' => 'dev-mq.a.pa.com',
    'port' => 5672,
    'user' => 'logs',
    'password' => 'guest',
    'vhost' => '/adhouse/',
    'connection_timeout' => 3.0,
    'read_write_timeout' => 3.0
], [
    'exchange' => 'exchange-test',
    'type' => 'fanout'
], [
    'queue_name' => 'queue-test',
    'auto_delete' => false,
    'durable' => false
], [
    'routing_key' => 'test',
    'auto_ack' => false, //true表示,客户端取到数据就删除rabbitmq服务器中数据,false表示等待客户端调用ack()方法之后再删除服务器数据(起到可靠作用)
    'prefetch_count' => 10,//一般设置10吧,客户端最多存放未处理的数据记录数
    'publish_confrim' => true
]);

$i = null;

while (true) {
    echo '--- loop ---' . PHP_EOL;

    try {
        // $consumer->ping();

        $rc = $consumer->consumeWithRetry(function ($msg) use ($consumer, &$i) {
            if ($i === null) {
                $i = (int)$msg;
            } else {
                ++$i;
                if ((int)$msg != $i) {
                    printf('%s != %d', $msg, $i);
                    exit();
                }
            }
            printf("%s == %d\n", $msg, $i);
            $consumer->ack();
        }, 1000);
    } catch (\Exception $e) {
        var_dump($e->getMessage());
        sleep(1);
    }
}
