<?php
/**
 * Created by PhpStorm.
 * User: songwenyao
 * Date: 2020/6/27
 * Time: 9:04 AM
 */
namespace App\HttpController\MQTest;

use App\Domain\CrmDataPublish;
use EasySwoole\Http\AbstractInterface\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class OneController extends Controller
{
    function send(){

        (new CrmDataPublish())->sendCrmData('hello world');
        return $this->writeJson(200);
    }

}
