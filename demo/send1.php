<?php
//发送队列 -- 使用默认vhost和默认交换机exchange
require __DIR__ . '/common.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$rabbitmqConfig = include __DIR__ . '/config.php';
$user_queue = $rabbitmqConfig['user_queue'];

//1.连接  默认vhost='/'
$connection = new AMQPStreamConnection($user_queue['host'], $user_queue['port'], $user_queue['user'], $user_queue['password']);
//2.打开channel
$channel = $connection->channel();
//使用默认交换机
//3.声明一个队列名及设置属性
$channel->queue_declare('hello_queue_name', false, false, false, false);
//3. 设置消息内容
$msg = new AMQPMessage('Hello World消息内容!');
//4.发送消息
$i = 0;
while (true) {
    $channel->basic_publish($msg, '', 'hello_queue_name');
    ++$i;
    if($i>10000) { //连续发1万之后 退出
       break;
    }
}

$channel->close();
$connection->close();

