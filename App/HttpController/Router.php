<?php
/**
 * Created by PhpStorm.
 * User: songwenyao
 * Date: 2020/6/27
 * Time: 9:00 AM
 */
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->post('/MQSend','/MQTest/OneController/send');
        $routeCollector->post('/MQReceive','/MQTest/OneController/receive');
    }

}
