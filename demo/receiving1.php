<?php
/**
 * 消费队列-- 使用默认vhost和默认交换机exchange
 */
require __DIR__ . '/common.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
//1.打开连接
$connection = new AMQPStreamConnection('dev-mq.a.pa.com', 5672, 'admin', 'admin');
//2.打开channel
$channel = $connection->channel();
//3.声明队列 其中hello_queue_name是队列名
$channel->queue_declare('hello_queue_name', false, false, false, false);

$callback = function($msg) {
    $str = " [x] Received " . $msg->body . "\n";
    echo isWin()?mb_convert_encoding($str, 'gbk', 'utf-8'):$str;

};
//5.消费队列，设置接收内容回调
$channel->basic_consume('hello_queue_name', '', false, true, false, false, $callback);

while(count($channel->callbacks)) {//守护进程消费
    $channel->wait();
}
