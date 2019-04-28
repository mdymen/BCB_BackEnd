<?php

    define("ALGORITMO_ELIMINATORIAS", "eliminatorias");
    define("ALGORITMO_LIGA","liga");
   // define("ALGORITMO_LIGA","liga");
  //  define("ALGORITMO_ELIMINATORIAS","eliminatorias");
    define("ALGORITMO_API","api");
    
//include_once(APPLICATION_PATH."/../library/Zend/Log.php");
//include_once(APPLICATION_PATH."/../library/Zend/Log/Writer/Stream.php");
include APPLICATION_PATH.'/helpers/jwt/JWT.php';
include APPLICATION_PATH.'/helpers/jwt/SignatureInvalidException.php';
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
    public $id;
    public $user;

    public function getId() {
        return $this->id;
    }

    public function getUser() {
        return $this->user;
    }

    public function preDispatch() {
 
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            $x = $this->getRequest()->isOptions();

            if ($x) {
                $res = $this->getRequest();
                return $res;
            }

            $request = new Zend_Controller_Request_Http();
            $token = $request->getHeader('Authorization');

            $decoded = JWT::decode($token, "fka7kum1", array('HS256'));

            $this->id = $decoded->id;
            $this->user = $decoded->username;    

        } catch (SignatureInvalidException $s) {
            $response = new Zend_Controller_Response_Http();
            $response->setHttpResponseCode(400);
            $this->setResponse($response);
            
            $result["data"]["erro"] = "Chave incorreta";
            
            $this->_helper->json($result);
        } catch (Exception $e) {
            $response = new Zend_Controller_Response_Http();
            $response->setHttpResponseCode(400);
            $this->setResponse($response);
            
            $result["data"]["erro"] = $e->getMessage();
            
            $this->_helper->json($result);
        }

      //  $this->logger = new Zend_Log();
      //  $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH."../bolaoLog_".date("Y_m_d").".txt");
       // $this->logger->addWriter($writer);


    }

    public function info($msg) {
        $this->logger->log($msg, Zend_Log::INFO);
    }

    public function error($msg) {
        $this->logger->log($msg, Zend_Log::ERR);
    }

    public function logAction() {
        $params = $this->_request->getParams();

        $file1 = APPLICATION_PATH."../bolaoLog_".$params["fecha"].".txt";
        $lines = file($file1);
        foreach($lines as $line_num => $line)
        {
            echo $line;
            echo "<br>";
        }
        die(".");
    }
    
    public function get($url) {
        $ch = curl_init();
            
        curl_setopt($ch, CURLOPT_URL, $url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);

        curl_close ($ch); 

        return $server_output;
    }

}