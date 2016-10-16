<?php

include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
include APPLICATION_PATH.'/models/championships.php';
class Admin_ResultadosController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $params = $this->_request->getParams();
        
        if (!empty($params['ronda']) && !empty($params['champ'])) {
            $ronda = $params['ronda'];
            $champ = $params['champ'];
            $m_obj = new Application_Model_Matchs();
            $matchs = $m_obj->load_matchs_byrodada($champ, $ronda);
            $matchs = $m_obj->setDatas($matchs);
            $this->view->matchs = $matchs;
        }
        
        $c_obj = new Application_Model_Championships();
        $this->view->championships = $c_obj->load();
        
    }
    
    public function registerAction() {}

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    public function salvarjogoAction() {
       
    }
    
}

