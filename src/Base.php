<?php
namespace CjsRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Base
{
    //连接配置
    protected $connection_options = [
        'host'=>'127.0.0.1',
        'port' => 5672,
        'user' => 'admin',
        'password' => 'admin',
        'vhost'              => '/',   //vhost名
        'insist'             => false,
        'login_method'       => 'AMQPLAIN',
        'login_response'     => null,
        'locale'             => 'en_US',
        'connection_timeout' => 3.0,
        'read_write_timeout' => 3.0,
        'context'            => null,
        'keepalive'          => false,
        'heartbeat'          => 0
    ];

    //交换机配置
    protected $exchange_options = [
        'exchange' => 'exchange_test', //交换机名
        'type' => 'direct',  //交换机类型
        'passive'     => false,
        'durable'     => false,
        'auto_delete' => false,
        'internal'    => false,
        'nowait'      => false,
        'arguments'   => null,
        'ticket'      => null
    ];
    //队列配置
    protected $queue_options = [
        'queue_name'            => '',  //队列名
        'passive'         => false,
        'durable'         => false,
        'exclusive'       => false,
        'auto_delete'     => true,
        'nowait'          => false,
        'arguments'       => null,
        'ticket'          => null
    ];
    //路由key
    protected $options = [
        'routing_key'     => '',
        'auto_ack' => false,
        'prefetch_count' => 1,
        'publish_confrim' => true
    ];
    //连接对象
    protected $connection;
    //渠道
    protected $channel;
    //交换机
    protected $exchange;
    //队列
    protected $queue;
    //是否已连接
    protected $connected = false;
    //是否初始化完毕
    protected $initiailized = false;

    public function __construct($connection_options = [], $exchange_options =[], $queue_options =[], $options =[])
    {
        $this->connection_options = array_merge($this->connection_options, $connection_options);
        $this->exchange_options = array_merge($this->exchange_options, $exchange_options);
        $this->queue_options = array_merge($this->queue_options, $queue_options);
        $this->options = array_merge($this->options, $options);
        $this->init();
    }

    protected function init(){
        if (!$this->initiailized) {
            $this->createConnection()->createChannel()->exchangeDeclare()->queueDeclare();
            $this->initiailized = true;
        }
    }

    public function reinit()
    {
        $this->uninit();
        $this->init();
        return $this;
    }

    public function __destruct()
    {
        $this->uninit();
    }

    protected function uninit()
    {
        if ($this->initiailized) {
            $this->initiailized = false;
            $this->destroyChannel();
            $this->destroyConnection();
        }
    }


    public function isConnected()
    {
        return $this->initiailized && $this->connected && $this->getConnection()->isConnected();
    }


    protected function createConnection()
    {
        if ($this->connection) {
            $this->connection->reconnect();
        } else {
            $this->connection = new AMQPStreamConnection(
                                                        $this->connection_options['host'],
                                                        $this->connection_options['port'],
                                                        $this->connection_options['user'],
                                                        $this->connection_options['password'],
                                                        $this->connection_options['vhost'],
                                                        $this->connection_options['insist'],
                                                        $this->connection_options['login_method'],
                                                        $this->connection_options['login_response'],
                                                        $this->connection_options['locale'],
                                                        $this->connection_options['connection_timeout'],
                                                        $this->connection_options['read_write_timeout'],
                                                        $this->connection_options['context'],
                                                        $this->connection_options['keepalive'],
                                                        $this->connection_options['heartbeat']
                                                    );
        }
        $this->connected = true;
        return $this;
    }

    protected function destroyConnection()
    {
        // safe close
        if ($this->connection) {
            try {
                $this->connection->close();
            } catch (\Exception $e) {
            }
        }
        $this->connected = false;
        return $this;
    }

    protected function createChannel()
    {
        $this->channel = $this->getConnection()->channel();
        return $this;
    }

    protected function destroyChannel()
    {
        // safe close
        if ($this->channel) {
            try {
                $this->channel->close();
            } catch (\Exception $e) {
            }

            $this->channel = null;
        }
    }

    protected function exchangeDeclare()
    {
        $this->channel->exchange_declare(
                                        $this->exchange_options['exchange'],
                                        $this->exchange_options['type'],
                                        $this->exchange_options['passive'],
                                        $this->exchange_options['durable'],
                                        $this->exchange_options['auto_delete'],
                                        $this->exchange_options['internal'],
                                        $this->exchange_options['nowait'],
                                        $this->exchange_options['arguments'],
                                        $this->exchange_options['ticket']
                                    );
        return $this;
    }

    protected function queueDeclare()
    {
        $this->channel->queue_declare(
                                        $this->queue_options['queue_name'],
                                        $this->queue_options['passive'],
                                        $this->queue_options['durable'],
                                        $this->queue_options['exclusive'],
                                        $this->queue_options['auto_delete'],
                                        $this->queue_options['nowait'],
                                        $this->queue_options['arguments'],
                                        $this->queue_options['ticket']
                                    );

        $this->channel->queue_bind(
                                    $this->queue_options['queue_name'],
                                    $this->exchange_options['exchange'],
                                    $this->options['routing_key']
                                );
        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getQueue()
    {
        return $this->queue_options['queue_name'];
    }

    public function getExchange()
    {
        return $this->exchange_options['exchange'];
    }

    public function getChannel()
    {
        return $this->channel;
    }
    //是否断开连接
    protected function causedByLostConnection($e)
    {
        $ret = false;
        $message = $e->getMessage();
        $rc = preg_match('/broken pipe|closed connection/i', $message);

        if (1 === $rc) {
            trigger_error(sprintf('%s (%s:%d)', $message, $e->getFile(), $e->getLine()), E_USER_WARNING);
            $this->connected = false;
            $ret = true;
        }
        return $ret;
    }

    public function ping()
    {
        $ret = false;
        if (!$this->isConnected()) {
            $this->reinit();
            $ret = true;
        }
        return $ret;
    }

}