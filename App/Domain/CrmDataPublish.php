<?php
/**
 * Created by PhpStorm.
 * User: songwenyao
 * Date: 2020/6/28
 * Time: 4:36 PM
 */
namespace App\Domain;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
class CrmDataPublish
{
    private $MQ;
    function __construct()
    {
        $this->MQ = new AMQPStreamConnection('39.105.187.200','5672','voy666','voy666');
    }

    function sendCrmData($message){
        try{
            $exchange_name = 'crm_data';
            $queue_name = 'crm_data';
            $channel = $this->MQ->channel(1);
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
            $channel->exchange_declare($exchange_name, AMQPExchangeType::DIRECT, false, true, false);
            $channel->queue_bind($queue_name, $exchange_name, $queue_name);
            $channel->set_ack_handler(
                function (AMQPMessage $message) {
                    //发送成功反馈
                    echo "Message ack with content " . $message->body . PHP_EOL;
                }
            );
            $channel->set_nack_handler(
                function (AMQPMessage $message){
                    //处理失败发送
                    echo "Message nacked with content " . $message->body . PHP_EOL;
                    $message->reject(false);
                }
            );
            $channel->confirm_select();
            for($i=0;$i<10;$i++){
                $msg = new AMQPMessage($message.$i, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
                $channel->basic_publish($msg, $exchange_name, $queue_name);
            }

            $channel->wait_for_pending_acks();
            $channel->close();

            return true;
        }catch (\Exception $e){
            //生产失败处理


        }
    }

    function __destruct()
    {
        $this->MQ->close();
    }
}
