<?php

include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
include APPLICATION_PATH.'/models/transaction.php';
//include APPLICATION_PATH.'/helpers/data.php';
class Admin_TransacoesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $params = $this->_request->getParams();
       
       $id_champ = $params['championship'];
       
       $u = new Application_Model_Championships();
       $champ = $u->load();
       $this->view->champs = $champ;
       $this->view->champ = $id_champ;
       
       if ($this->getRequest()->isPost()) {
           $t = new Application_Model_Transaction();
           $transactions = $t->getCampeonato($id_champ);
           $this->view->transactions = $transactions;
           
           print_r($transactions);
       }
       
    }
    
    public function registerAction() {}

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    
}

