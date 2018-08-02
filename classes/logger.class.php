<?php
class Logger
{
    private $registry;
    private $logFilePath;
    function __construct($registry){
        $this->registry = $registry;
        $this->logFilePath = ROOT_DIR.'log';
    }

    public function log($message)
    {
        $h = fopen($this->logFilePath, 'a+');
        flock($h, LOCK_EX);
        fwrite($h, $message . "\n");
        fflush($h);
        flock($h, LOCK_UN);
        fclose($h);
    }
}