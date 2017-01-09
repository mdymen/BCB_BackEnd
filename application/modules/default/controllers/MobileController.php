<?php
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/pencas.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH."/helpers/html.php";
include APPLICATION_PATH."/helpers/translate.php";
include APPLICATION_PATH.'/helpers/box.php';
include APPLICATION_PATH.'/helpers/mail.php';
class MobileController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_id'];
    }
    
    public function getEmailUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_email'];
    }    
    
    public function getUserData() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data;
    }
 
    function logoutAction() {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        $this->_redirect();
    }           
    
    public function loginAction() {
        $params = $this->_request->getParams();
        
        $user = $params["username"];
        $password = $params["password"];
        
        $error = false;
        
        $users = new Application_Model_Users();
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'user');
        $authAdapter->setIdentityColumn('us_username')
                    ->setCredentialColumn('us_password');
        $authAdapter->setIdentity($user)
                    ->setCredential($password);

        $result = $auth->authenticate($authAdapter);
        
        if ($result->isValid()) {         
            $storage = new Zend_Auth_Storage_Session();
            $storage->write($authAdapter->getResultRowObject());
            $this->_redirect('/index');    
        } else {
            $error = true;
            $this->_redirect('/index?error=yes');
        }

    }
   
    public function celloginAction() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        
        $this->login1($data['us'], $data['pass']);
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json("loginok"); 
    }
    
    public function login1($usuario, $pass) {
        //$params = $this->_request->getParams();
        
        $user = $usuario;
        $password = $pass;
        
//        print_r($params);
//        die(".");
        
        $users = new Application_Model_Users();
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'user');
        $authAdapter->setIdentityColumn('us_username')
                    ->setCredentialColumn('us_password');
        $authAdapter->setIdentity($user)
                    ->setCredential($password);

        $result = $auth->authenticate($authAdapter);
        
        if ($result->isValid()) {         
            $storage = new Zend_Auth_Storage_Session();
            $storage->write($authAdapter->getResultRowObject());
            
        }
        
//        $this->_redirect('/index');
    }
    
    public function celproximojogosAction() {
       // $params = $this->_request->getParams();
        
        $us_id = 1;//$params['us_id'];
        
        $m = new Application_Model_Matchs();
        $matchs = $m->proximos_jogos($us_id);

        $this->view->matchs = $matchs;
            
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($matchs);      
    }
    
    public function celmeuspalpitesnaojogadosAction() {
        
    }
    
    public function celaddprovisorioAction() {
        
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
            
        $us = $data['us'];
        $pass = $data['pass'];
        
        $u = new Application_Model_Users();
        $u->addprovisorio($us, $pass);
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(1); 
        
    }
    
}

