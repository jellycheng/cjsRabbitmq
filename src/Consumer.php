<?php
namespace CjsRabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class Consumer extends Base {

    private $auto_ack;
    private $consumer_tag;
    private $last_delivery_tag;

    public function init()
    {
        parent::init();
        if (isset($this->options['prefetch_count'])) {
            $this->getChannel()->basic_qos(null, $this->options['prefetch_count'], null);
        }
        $this->auto_ack = !isset($this->options['auto_ack']) || $this->options['auto_ack'];
    }

    public function uninit()
    {
        if ($this->isConnected()) {
            $this->cancel();
        }
        $this->consumer_tag = null;
        $this->last_delivery_tag = null;
        parent::uninit();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function pop()
    {
        try {
            $message = $this->getChannel()->basic_get($this->getQueue(), $this->auto_ack);
        } catch (\Exception $e) {
            $this->causedByLostConnection($e);
            throw $e;
        }
        $ret = null;
        if (isset($message)) {
            if (!$this->auto_ack) {
                $this->last_delivery_tag = $message->delivery_info['delivery_tag'];
            }
            $ret = $message->body;
        }
        return $ret;
    }

    public function popWithRetry()
    {
        try {
            $ret = $this->pop();
        } catch (\Exception $e) {
            if (!$this->ping()) {
                throw $e;
            } else {
                $ret = $this->pop();
            }
        }
        return $ret;
    }

    public function consume($callback = null, $timeout = 0, $exclusive = false)
    {
        $ret = true;
        try {
            if (!isset($this->consumer_tag)) {
                $this->consumer_tag = $this->getChannel()->basic_consume(
                                                                        $this->queue,
                                                                        '',
                                                                        false,
                                                                        $this->auto_ack,
                                                                        $exclusive,
                                                                        false,
                                                                        function ($message) use ($callback) {
                                                                            if (!$this->auto_ack) {
                                                                                $this->last_delivery_tag = $message->delivery_info['delivery_tag'];
                                                                            }

                                                                            return call_user_func($callback, $message->body);
                                                                        }
                                                                    );
            }

            $this->getChannel()->wait(null, false, $timeout / 1000);
            // $this->getChannel()->wait();
        } catch (AMQPTimeoutException $e) {
            $ret = false;
        } catch (\Exception $e) {
            $this->causedByLostConnection($e);
            throw $e;
        }
        return $ret;
    }

    public function consumeWithRetry($callback = null, $timeout = 0, $exclusive = false)
    {
        try {
            $ret = $this->consume($callback, $timeout, $exclusive);
        } catch (\Exception $e) {
            if (!$this->ping()) {
                throw $e;
            } else {
                $ret = $this->consume($callback, $timeout, $exclusive);
            }
        }
        return $ret;
    }

    public function ack()
    {
        if (isset($this->last_delivery_tag)) {
            try {
                $this->getChannel()->basic_ack($this->last_delivery_tag);
            } catch (\Exception $e) {
                $this->causedByLostConnection($e);
                throw $e;
            }

            $this->last_delivery_tag = null;
        }
    }

    public function nack()
    {
        if (isset($this->last_delivery_tag)) {
            try {
                $this->getChannel()->basic_nack($this->last_delivery_tag);
            } catch (\Exception $e) {
                $this->causedByLostConnection($e);
                throw $e;
            }
            $this->last_delivery_tag = null;
        }
    }

    public function cancel()
    {
        if (isset($this->consumer_tag)) {
            try {
                $this->getChannel()->basic_cancel($this->consumer_tag);
                $this->consumer_tag = null;
            } catch (\Exception $e) {
                $this->causedByLostConnection($e);
                throw $e;
            }
        }
    }
    
}