<?php
namespace CjsRabbitmq;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class Producer extends Base {

    public function push($message, $timeout = 0)
    {
        $message = new AMQPMessage($message,
                                    [
                                        'content_type' => 'text/plain',
                                        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
                                    ]);
        try {
            $this->channel->basic_publish($message, $this->getExchange(), $this->options['routing_key']);
        } catch (\Exception $e) {
            $this->causedByLostConnection($e);
            throw $e;
        }
        return true;
    }

    public function pushWithRetry($message, $timeout = 0)
    {
        try {
            $ret = $this->push($message, $timeout);
        } catch (\Exception $e) {
            if(!$this->ping()) {
                throw $e;
            } else {
                $ret = $this->push($message, $timeout);
            }
        }
        return $ret;
    }

    public function txPush($message, $timeout = 0)
    {
        $message = new AMQPMessage($message,
                                    [
                                        'content_type' => 'text/plain',
                                        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
                                    ]);

        try {
            $this->channel->tx_select();
            $this->channel->basic_publish($message, $this->getExchange(), $this->options['routing_key']);
            $this->channel->tx_commit();
        } catch (\Exception $e) {
            $this->causedByLostConnection($e);
            throw $e;
        }
        return true;
    }

    public function txPushWithRetry($message, $timeout = 0)
    {
        try {
            $ret = $this->txPush($message, $timeout);
        } catch (\Exception $e) {
            if (!$this->ping()) {
                throw $e;
            } else {
                $ret = $this->txPush($message, $timeout);
            }
        }
        return $ret;
    }
    
}