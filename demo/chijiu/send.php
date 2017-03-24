<?php
/**
 * 发消息
 */
require  dirname(__DIR__) . '/common.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$host = 'localhost';
$port = 5672;
$username = 'guest';
$pwd = 'guest';
$connection = new AMQPStreamConnection($host, $port, $username, $pwd);
$channel = $connection->channel();
$channel->queue_declare('task_queue', false, true, false, false);

#消息内容
$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "Hello World 8";
#消息对象
$msg = new AMQPMessage($data,
                        array('delivery_mode' => 2) # make message persistent
                        );
#向对列中发消息
$channel->basic_publish($msg, '', 'task_queue');

$channel->close();
$connection->close();

