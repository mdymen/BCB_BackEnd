<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PencaController
 *
 * @author Martin Dymenstein
 */
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/pencas.php';
class PencaController extends Zend_Controller_Action {
    
    public function indexAction() {
        $params = $this->_request->getParams();
        $id_penca = $params['penca'];
        
        $penca = new Application_Model_Penca();
        $participantes = $penca->load_participantes($id_penca);
        
        $info_penca = $penca->load_penca($id_penca);
        
    
        
        $teams = new Application_Model_Teams();
        $teams = $teams->load_penca_limit($info_penca[0]['pn_idchampionship'], 10);

        $this->view->info_penca = $info_penca;
        $this->view->teams = $teams;
        $this->view->participantes = $participantes;
    }
    
    public function listAction() {
        
    }
    
    public function proximostimesAction() {
        $params = $this->_request->getParams();
        
        $proximos = $params['proximos'];
        $penca = $params['penca'];
        
        
    }
    
    public function usuariospencaAction() {
        $params = $this->_request->getParams();
        
        $id_penca = $params['penca'];
        
        $penca = new Application_Model_Penca();
        $usuarios = $penca->load_usuarios($id_penca);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($usuarios);
    }
}
