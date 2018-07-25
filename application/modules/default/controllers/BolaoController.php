<?php

include_once(APPLICATION_PATH."/../library/Zend/Log.php");
include_once(APPLICATION_PATH."/../library/Zend/Log/Writer/Stream.php");
class BolaoController extends Zend_Controller_Action
{

    /*
    EMERG   = 0;  // Emergency: system is unusable
    ALERT   = 1;  // Alert: action must be taken immediately
    CRIT    = 2;  // Critical: critical conditions
    ERR     = 3;  // Error: error conditions
    WARN    = 4;  // Warning: warning conditions
    NOTICE  = 5;  // Notice: normal but significant condition
    INFO    = 6;  // Informational: informational messages
    DEBUG   = 7;  // Debug: debug messages
    */

    public $logger;

    public function init() {
        $this->logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH."../bolaoLog.txt");
        $this->logger->addWriter($writer);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function info($msg) {
        $this->logger->log($msg, Zend_Log::INFO);
    }

    public function error($msg) {
        $this->logger->log($msg, Zend_Log::ERR);
    }

    public function logAction() {
        $file1 = APPLICATION_PATH."../bolaoLog.txt";
        $lines = file($file1);
        foreach($lines as $line_num => $line)
        {
            echo $line;
            echo "<br>";
        }
        die(".");
    }

}