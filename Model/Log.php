<?php


namespace Indrajit\FacebookPixel\Model;

use Indrajit\FacebookPixel\Logger\Logger;
class Log
{
    protected  $logger;
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

}
