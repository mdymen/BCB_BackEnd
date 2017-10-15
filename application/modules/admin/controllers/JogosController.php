<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/helpers/translate.php';
class Admin_JogosController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $params = $this->_request->getParams();        
        
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load();
        
        if (!empty($params['champ'])) {            
            $t_obj = new Application_Model_Matchs();
            $teams = $t_obj->load_all_matchs($params['champ']);
            $this->view->matches = $teams;
            $this->view->champ = $params['champ'];                        
        }       
    }
    

    public function excluirjogoAction() {
        $params = $this->_request->getParams();
        
        $match = $params['match'];
        
        $t_obj = new Application_Model_Matchs();
        $t_obj->delete("mt_id = ".$match);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
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
    
    public function editAction() {
        $params = $this->_request->getParams();
        
        $id_match = $params['jogo'];
        
        $m = new Application_Model_Matchs();
        $match = $m->load_match($id_match);
        
        $c = new Application_Model_Championships();
        $rondas = $c->getrondas($match['rd_idchampionship']);
        
        $t = new Application_Model_Teams();
        $teams = $t->load_teams_championship($match['rd_idchampionship']);
        
        $this->view->match = $match;
        $this->view->rondas = $rondas;        
        $this->view->teams = $teams;
    }
    
    public function salvareditAction() {
        $params = $this->_request->getParams();
        
        $m = new Application_Model_Matchs();
        $m->atualizar_match($params);
        
        
        $this->redirect("admin/jogos/index", array('champ' => $params['mt_idchampionship']));
    }
    
    public function jogospordataAction() {
        $params = $this->_request->getParams();
        
        if (!empty($params['mt_date_ini']) || !empty($params['mt_date_fim'])) {
            $date_ini = $params['mt_date_ini'];
            $date_fim = $params['mt_date_fim'];           
            
            $m = new Application_Model_Matchs();
            $matchs = $m->load_by_date($date_ini, $date_fim);
            
            $this->view->matchs = $matchs;
        }
    }
}

