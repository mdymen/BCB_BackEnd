<?php

include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/pencas.php';
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
//        print_r("HOLA");
//        die(".");
        
        //Retorna las posiciones de todas las pencas 
        //en la cual el usuario participa.
        //en verde al ganador con el puntaje
        //en amarillo los siguientes
        // en rojo los que no ganan nada
        
        //listado de pencas aonde o usuario pode
        //participar porque são livres
       
        //utimos resultados de alguna penca o de todas?
        
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
   
//        print_r($data);
//        die(".");
//        
        if (!empty($data)) {
            $result = new Application_Model_Result();
            $em_acao = $result->palpites_em_acao($data['us_id']);
            $points = $result->points($data['us_id']);
            
            $this->view->em_acao = $em_acao;
            $this->view->points = $points;
        //$pencas = $penca->load_penca__puntagem_usuario($data['us_id']);
        
//            $pencas = $penca->load_pencas();
//            $pencas_usuario = $penca->load_pencas_usuario($data['us_id']);
//
//            $this->view->pencas = $pencas;
//            $this->view->pencas_usuario = $pencas_usuario;
//        
        }
    }
    
    public function registerAction() {
       $params = $this->_request->getParams();
          
       $user = new Application_Model_Users();
       $user->save($params);
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
        
        $this->_redirect('/index');
    }
    
    public function registerteamsAction() {
        
    }   
    
    public function registerteamsformAction() {
        $params = $this->_request->getParams();
        
        print_r($params["teams"]);
        die(".");
        
        $teams = explode(' ', $params["teams"]);
        print_r($teams);
        die(".");
    }
    
   
}

