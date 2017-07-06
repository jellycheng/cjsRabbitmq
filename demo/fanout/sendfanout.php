<?php
/**
 * 发送广播消息
 * http://dev-mq.a.pa.com/#/
 */

require_once dirname(__DIR__) . '/common.php';

use CjsRabbitmq\Producer;

$producer = new Producer([
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
    'durable' => false,
    'auto_delete' => false,
], [
    'routing_key' => '',
    'publish_confirm' => true
]);

$i = 0;

while (true) {
    echo '--- loop ---' . PHP_EOL;

    try {
        // if ($producer->ping()) {
        // var_dump('reconnect');
        // }

        $i++;
        $rc = $producer->txPushWithRetry($i, 3000);
        // $rc = $producer->pushWithRetry($i, 3000);
        var_dump('---------------------------------------------' . $i);
    } catch (\Exception $e) {
        throw $e;
        var_dump($e->getMessage());
    }

    usleep(100000);
}
