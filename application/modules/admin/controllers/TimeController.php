<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
include APPLICATION_PATH.'/helpers/translate.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
//include APPLICATION_PATH.'/helpers/data.php';
class Admin_TimeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $params = $this->_request->getParams();
       
       $champ = new Application_Model_Championships();
       
       $this->view->champs = $champ->load();
       
    }
    
    /**
     * Graba la lista de equipos
     * @param equipos es la lista de equipos para grabar,
     * esta lista tiene como atributos:
     * @param tm_name
     * @param tm_logo
     * @param tm_idchampionship
     * @param tm_grupo
     * 
     */
    public function salvartimeAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
     
        $time = new Application_Model_Teams();
        
        $equipos = $params['equipos'];
        for ($i = 0; $i < count($equipos); $i = $i + 1) {
            $time->save(
                array(
                    'tm_name' => $nome, 
                    'tm_logo' => $logo, 
                    'tm_idchampionship' => $champ, 
                    'tm_grupo' => $grupo
                )
            );
        }    
     
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
}

