<?php
/**
 * 发消息 - 广播型
 */
require  dirname(__DIR__) . '/common.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
#声明fanout类型的交换机
$channel->exchange_declare('exchage_name1', 'fanout', false, false, false);

$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "info: Hello World!";
$msg = new AMQPMessage($data);
#发消息
$channel->basic_publish($msg, 'exchage_name1');

echo " [x] Sent ", $data, "\n";

$channel->close();
$connection->close();
