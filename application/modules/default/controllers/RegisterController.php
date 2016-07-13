<?php

include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/championships.php';
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
  
}

