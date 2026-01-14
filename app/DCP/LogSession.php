<?php

namespace App\DCP;

class LogSession
{


    public $logPath = NULL;

    public function writeLineToLog($logMessage)
    {

        $this->logPath = storage_path('log.txt');

        $file = fopen($this->logPath, 'a');

        fwrite($file, $logMessage . "\n");
    }
}
