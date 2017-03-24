<?php
require_once __DIR__ . '/common.php';
/**
 * 消费消息 示例
 */

use CjsRabbitmq\Consumer;
$consumer = new RabbitmqConsumer([
                            'host' => '127.0.0.1',
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
                            'queue' => 'queue-test',
                            'auto_delete' => false,
                            'durable' => false
                        ], [
                            'routing_key' => 'test',
                            'auto_ack' => false,
                            'prefetch_count' => 1,
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

