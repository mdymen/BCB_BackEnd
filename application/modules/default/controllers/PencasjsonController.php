<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TeamController
 *
 * @author Martin Dymenstein
 */
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/models/pencas.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
class PencasjsonController extends Zend_Controller_Action
{
    public function indexAction() {
        
    }
    
    public function testAction() {
        $p = new Application_Model_Penca();
        
        $x = $p->load_pencas();
        $this->_helper->json($x);
    }
    
    public function teamAction() {
        $params = $this->_request->getParams();
        
        $team = $params['team'];
        $champ = $params['champ'];
        
        $ob_team = new Application_Model_Teams();
        
        $jogos = $ob_team->getJogosTeam($team, $champ);
        
        $this->view->jogos = $jogos;
    }
    
    public function addteamAction() {
        $params = $this->_request->getParams();
        
        $team = new Application_Model_Team();
        $team->insert($params);
        
        
    }
    
    public function parseteamsAction() {
        
    }
    
}
