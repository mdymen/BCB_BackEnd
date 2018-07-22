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
include APPLICATION_PATH.'/models/equipo.php';
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

    /**
     * Recibe una lista de equipos para grabar
     * 
     * @param equipos lista de equipos a adicionar
     *  @param idPais id do pais do equipo
     *  @param logo url do logo do equipo
     *  @param nome nombre del equipo
     */
    public function postAction() {   
        $this->getResponse()->setHeader('Content-Type', 'application/json');
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {      
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);
                
            $equipos = $data['equipos'];
            $e = new Application_Model_Equipo();

            for ($i = 0; $i < count($equipos); $i = $i + 1) {
                $equipo = $equipos[$i];
                
                $nome = $equipo['nome'];
                $pais = $equipo['idPais'];
                $logo = $equipo['logo'];
                $sigla = $equipo['sigla'];

                $result = $e->save($nome, $pais, $logo, $sigla);
            }


            $body = array();
            $body['body'] = $e->loadByPais($equipos[0]['idPais']);
            
            $this->_helper->json($body); 
        } catch (Exception $e) {
            $this->_helper->json($e); 
        }
    }

    /**
     * GET
     * Retorna todos los equipos cadastrados de ese pais
     * @param idPais
     */
    public function getbypaisAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {      
            $body = $this->getRequest()->getParams();
                
            $idpais = $body['idPais'];
            $e = new Application_Model_Equipo();

            $body = array();
            $body['body'] = $e->loadByPais($idpais);
            
            $this->_helper->json($body); 
        } catch (Exception $e) {
            $this->_helper->json($e); 
        }
    }

    public function getAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $e = new Application_Model_Equipo();
        $body = array();
        $body['body'] = $e->load();

        $this->_helper->json($body);
    }

    
}
