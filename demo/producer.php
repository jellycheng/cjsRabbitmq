<?php
require_once __DIR__ . '/common.php';
/**
 * 生产消息 示例
 */

use CjsRabbitmq\Producer;

$producer = new Producer([
    'host' => '127.0.01',
    'port' => 5672,
    'user' => 'admin',
    'password' => 'admin',
    'vhost' => '/push/',
    'connection_timeout' => 3.0,
    'read_write_timeout' => 3.0
], [
    'exchange' => 'exchange-test',
    'type' => 'direct'
], [
    'queue_name' => 'queue-test',
    'durable' => false,
    'auto_delete' => false,
], [
    'routing_key' => 'test',
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
