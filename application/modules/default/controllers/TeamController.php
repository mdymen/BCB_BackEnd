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
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/box.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
class TeamController extends Zend_Controller_Action
{
    public function indexAction() {
        
    }
    
    public function teamAction() {
        $params = $this->_request->getParams();
        
        $team_id = $params['team'];
        $champ = $params['champ'];
        
        $ob_team = new Application_Model_Teams();
        
        $jogos = $ob_team->getJogosTeam($team_id, $champ);
        
        for ($i = 0; $i < count($jogos); $i = $i + 1) {
            if ($jogos[$i]['tm1_id'] == $team_id) {
                $team = $jogos[$i]['t1nome'];
            }
            if ($jogos[$i]['tm2_id'] == $team_id) {
                $team = $jogos[$i]['t2nome'];
            }
            $jogos[$i]['rs_res1'] = "";
            $jogos[$i]['rs_res2'] = "";
            $jogos[$i]['rs_result'] = "";
        }
        
        $this->view->nome_team = $team;
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
