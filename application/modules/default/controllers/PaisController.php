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
include APPLICATION_PATH.'/models/pais.php';
include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/box.php';
class PaisController extends Zend_Controller_Action
{

    public function indexAction() {}

    public function postAction() {        
        
        $this->getResponse()->setHeader('Content-Type', 'application/json');
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {      
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);
                
            $pais = $data['pais'];

            $e = new Application_Model_Pais();
            $result = $e->save($pais);
            $body = array();
            $body['body'] = $result;
            
            $this->_helper->json($body); 
        } catch (Exception $e) {
            $this->_helper->json($e); 
        }
    }

    public function putAction() {}

    /**
     * Carga todos los paises cadastrados en el sistema
     */
    public function getAction() {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {      
            $e = new Application_Model_Pais();
            $body['body'] = $e->load();
            
            $this->_helper->json($body); 
        } catch (Exception $e) {
            $this->_helper->json($e); 
        }
    }

    public function deleteAction() {}



}