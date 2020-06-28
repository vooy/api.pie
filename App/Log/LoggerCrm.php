<?php
/**
 * Created by PhpStorm.
 * User: songwenyao
 * Date: 2020/6/28
 * Time: 4:17 PM
 */
namespace App\Log;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Log\Logger;

class LoggerCrm extends Logger
{
    use Singleton;
    private $loggerWriter;
    private $defaultDir;
    private $file;

    function __construct(){
        $this->defaultDir = Config::getInstance()->getConf('LOG_DIR');
    }

    function log($msg, $logLevel = 1, $category = 'crm')
    {
        $str = date("y-m-d H:i:s")." "."{$msg}\n";
        $filePrefix = $category."_".date('Ymd');
        $filePath = $this->defaultDir."/{$filePrefix}.log";
        file_put_contents($filePath,$str,FILE_APPEND|LOCK_EX);
        return $this;
    }


}
