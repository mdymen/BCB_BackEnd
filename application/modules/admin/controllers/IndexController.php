<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';

include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/translate.php';
class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $params = $this->_request->getParams();
        
        $penca = new Application_Model_Championships();
        
        $champs = $penca->load();
        
        if (!empty($params['champ'])) {            
            $t_obj = new Application_Model_Teams();
            $teams = $t_obj->load_teams_para_jogo($params['champ']);
            
            $c = new Application_Model_Championships();
            
            $this->view->rondas = $c->getrondas($params['champ']);        
            
//            print_r($this->view->rondas);
            
            $this->view->teams = $teams;
            $this->view->champ = $params['champ'];
        }
        
        $this->view->championships = $champs;
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
    
    
    public function usuariosAction() {
        
        $u = new Application_Model_Users();
        
        $users = $u->users();
        
        $this->view->users = $users;
        
    }
           
}

