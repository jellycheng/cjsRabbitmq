<?php
return array(
    'user_queue'=>array(
        'host' => '127.0.0.1', //dev-mq.a.pa.com
        'port' => 5672,
        'user' => 'guest', //admin
        'password' => 'guest', //admin
        'vhost' => '/user-queue-test/',
        'connection_timeout' => 3.0,
        'read_write_timeout' => 3.0

    ),

);