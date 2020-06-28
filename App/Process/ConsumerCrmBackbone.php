<?php
/**
 * Created by PhpStorm.
 * User: songwenyao
 * Date: 2020/6/28
 * Time: 10:55 AM
 */
namespace App\Process;

use EasySwoole\Component\Process\AbstractProcess;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class ConsumerCrmBackbone extends AbstractProcess
{
    protected function run($arg)
    {
        try{
            $exchange_name = 'crm_data_backbone';
            $queue_name = 'crm_data_backbone';
            $MQ = new AMQPStreamConnection('39.105.187.200','5672','voy666','voy666');
            $channel = $MQ->channel();
            $callback = function ($msg) {
                echo ' write text msg ', $msg->body, "\n";
                $msg->ack();
            };
            $channel->queue_bind($queue_name, $exchange_name, $queue_name);
            $channel->basic_consume($queue_name, 'consumer', false, false, false, false, $callback);

            while ($channel->is_consuming()) {
                $channel->wait();
            }
            $channel->close();
            $MQ->close();
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

}
