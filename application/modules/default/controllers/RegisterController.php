<?php

include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/championships.php';
include APPLICATION_PATH.'/models/pencas.php';
class RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function championshipAction() {

    }
    
    public function addchampionshipAction() {
        $params = $this->_request->getParams();
        
        $championship = new Application_Model_Championships();
        $championship->save($params);
        
        $this->redirect("/register/championship");
    }
    
    public function teamAction() {
        
    }
    
    public function addteamAction() {
        $params = $this->_request->getParams();
        
        $championship = new Application_Model_Teams();
        $championship->save($params);
        
        $this->redirect("/register/team");   
    }        
    
    public function pencaAction() {
        
    }
    
    public function addpencaAction() {
        $params = $this->_request->getParams();
        
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();

        $params['pn_iduser'] = $data->us_id;
        
        $penca = new Application_Model_Penca();
        $penca->save($params);
        
        $params['up_idpenca'] = $params['tm_idchampionship'];
        $params['up_iduser'] = $data->us_id;
        
        $penca->save_userpenca($params);
        
        $this->redirect("/register/penca");
    }
    
    public function matchAction() {}
    
    public function addmatchAction() {
        
    }
    
  
}

