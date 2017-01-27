<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
include APPLICATION_PATH.'/models/transaction.php';
include APPLICATION_PATH.'/helpers/translate.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
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
           
           $usuario = "";
           $rodada = "";
           
           if (!empty($params['usuario'])) {
                $usuario = $params['usuario'];
                $this->view->usuario = $usuario;
           }
           
           if (!empty($params['rodada'])) {
                $rodada = $params['rodada'];
                $this->view->rodada = $rodada;
           }
           
           $m = new Application_Model_Matchs();
           $rondas = $m->getrondas($id_champ);
           $usuarios = $m->getusuarios_do_campeonato($id_champ);
           
           $t = new Application_Model_Transaction();
           $transactions = $t->getCampeonato($id_champ, $usuario, $rodada);
           $this->view->transactions = $transactions;
           $this->view->rondas = $rondas;
           $this->view->usuarios = $usuarios;
           
 
           
       }
       
    }
    
    public function registerAction() {}

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    
}

