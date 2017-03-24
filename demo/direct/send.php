<?php
/**
 * 向指定key中发消息
 * php demo/demo4/send.php info
 */
require  dirname(__DIR__) . '/common.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
#声明交换机
$channel->exchange_declare('direct_logs9', 'direct', false, false, false);

$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';#路由key
$data = implode(' ', array_slice($argv, 2));
if(empty($data)) $data = "Hello World999";

$msg = new AMQPMessage($data);
#发消息
$channel->basic_publish($msg, 'direct_logs9', $severity);

echo " [x] Sent ",$severity,':',$data," \n";

$channel->close();
$connection->close();

