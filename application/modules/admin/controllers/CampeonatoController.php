<?php

include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
//include APPLICATION_PATH.'/helpers/data.php';
class Admin_CampeonatoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $params = $this->_request->getParams();
       
       if (!empty($params['campeonato'])) {
           $champ = new Application_Model_Championships();
           $champ->save(array("ch_nome" => $params['campeonato'], "ch_atualround" => 1));
       }
    }
    
    public function registerAction() {}

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    public function salvarjogoAction() {
        $params = $this->_request->getParams();
        
        $ronda = $params['ronda'];
        $date = $params['date'];
        $hora = $params['hora'];
        $team1 = $params['team1'];
        $team2 = $params['team2'];
        $champ = $params['champ'];  
        
        $helper = new Helpers_Data();
        $date = $helper->for_save($date);
        
        $info = array(
            'round' => $ronda, 
            'team1' => $team1,
            'team2' => $team2,
            'date' => $date.' '.$hora,
            'championship' => $champ);
        
        $m = new Application_Model_Matchs();
        $m->save($info);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
}

