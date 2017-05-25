<?php
/**
 * rabbitmq配置
 */
return [
    'test_1' => [
        'connection' => [   //连接配置
            'host' => 'dev-mq.a.pa.com',
            'port' => 5672,
            'user' => 'admin',
            'password' => 'admin',
            'vhost' => 'user-service',
            'connection_timeout' => 3.0,
            'read_write_timeout' => 3.0
        ],
        'exchange' => [     //消息交换机配置
           'exchange' => 'exchange_search_agent',
           'passive' => false,
            'type' => 'direct',
            'durable' => true,
            'auto_delete' => false,
        ],
        'queue' => [        //消息队列载体配置
            'queue_name' => 'queue_search_agent',
            'durable' => true,
            'auto_delete' => false,
        ],
        'routing' => [      //路由关键字
            'routing_key' => 'routing_search_agent',
            'publish_confirm' => true,
            'auto_ack' => false,
            'prefetch_count' => 10,
        ]
    ],
];
