<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChampionshipController
 *
 * @author Martin Dymenstein
 */
include APPLICATION_PATH.'/models/pencas.php';
class CampeonatosController extends Zend_Controller_Action
{
    public function indexAction() {
        $params = $this->_request->getParams();
                    
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load();
        
        $p_obj = new Application_Model_Penca();
        
        if (!empty($params['champ'])) {  
        
            $this->view->teamuserid = $this->getTimeUserId();
            $this->view->teamusername = $this->getTimeUserName();
            
            $champ_id = $params['champ'];

            $this->view->champ = $champ_id;
            
            if (empty($params['rodada'])) {
                $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
            } else {            
                $rodada_id = $params['rodada'];
            }
            
            $this->view->rodada = $rodada_id;

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            $p_obj = new Application_Model_Penca();
            
            if (empty($params['team'])) {
                $rodadas = $matchs_obj->load_rodada($champ_id, $rodada_id, $data['us_id']);
                $this->view->porteam = true;
                $this->view->porrodada = false;
            } else {
                $this->view->porteam = false;
                $this->view->porrodada = true;
                $team_id = $params['team'];
                $rodadas = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $data['us_id']);
            }

            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 

            $this->view->teams = $teams;
            $this->view->rodadas = $rodadas;
            
            $this->view->rondas = $rondas;   
        }
    }
    
    public function getTimeUserId() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        
        return $data['us_team'];
    }
    
    public function getTimeUserName() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        
        return $data['us_teamname'];
    }

}