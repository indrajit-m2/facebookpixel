<?php


namespace Indrajit\FacebookPixel\Logger;
use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    protected $loggerType = Logger::INFO;
    protected $fileName = '/var/log/Indrajit_FacebookPixel.log';

}
