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

class ConsumerCrm extends AbstractProcess
{
    protected function run($arg)
    {
        try{
            $exchange_name = 'crm_data';
            $queue_name = 'crm_data';
            $MQ = new AMQPStreamConnection('39.105.187.200','5672','voy666','voy666');
            $channel = $MQ->channel();
            $callback = function ($msg) {

                try{
                    //throw new \Exception('some error');
                    echo ' [x] Received ', $msg->body, "\n";
                    $msg->ack();
                }catch (\Exception $e){
                    echo 'received exception',"\n";
                    $msg->nack();
                }

            };
            $x_args = new AMQPTable([
                'x-message-ttl'=>3600000,
                'x-max-length'=>100000,
                'x-overflow'=>'reject-publish-dlx',
                'x-dead-letter-exchange'=>'crm_data_backbone',
                'x-dead-letter-routing-key'=>'crm_data_backbone'
            ]);
            $channel->queue_declare($queue_name, false, true, false, true, false,
                $x_args
            );
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
