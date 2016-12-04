<?php

/*
 * EXEMPLO
 * 
 *     $mail = Bobby_Mail::getInstance();

       $mail->addTo('msn@dymenstein.com');
       $mail->setSubject('Testing 2');
       $mail->setBodyHtml('prueba framework bobby!!!');
       $mail->setFrom('msn@dymenstein.com', 'Martin Dymenstein');

       print_r($mail->send());
 */

class Helpers_Mail {
    
    private static $mail = null;
    private $_send_mail;
    
    private function __construct() {
        $config = array(
            'auth' => 'login',
            'username' => 'bolaocraquedebola16@gmail.com',
            'password' => 'Smd5yy00',
            'ssl' => 'tls',
            'port' => 587
        );
        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
        Zend_Mail::setDefaultTransport($mailTransport);
        
        $this->_send_mail = new Zend_Mail('UTF-8');
        
        $this->_send_mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
        
        //$this->_send_mail->addHeader("Content-Type", "text/html; charset=UTF-8");
    }
    
    public static function getInstance() {
        if (self::$mail == null) {
            self::$mail = new Helpers_Mail();
        }
        return self::$mail;
    }
    
    public function addTo($mail) {
        $this->_send_mail->addTo($mail);
        return $this;
    }
    
    public function setSubject($subject) {
        $this->_send_mail->setSubject($subject);
        return $this;
    }
    
    public function setBodyText($body) {
         $this->_send_mail->setBodyHtml(myConvert($body),
            null,
            Zend_Mime::ENCODING_7BIT);
        return $this;
    }
    
    
    public function setBodyHtml($body) {
        $this->_send_mail->setBodyHtml($body);
        return $this;
    }
    
    public function setFrom($mail,$name) {
        $this->_send_mail->setFrom($mail,$name);
        return $this;
    }
    
    public function send() {
        return $this->_send_mail->send();
    }
}
