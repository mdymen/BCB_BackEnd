<?php
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
//include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/translate.php';
class Admin_RodadaController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {     
        
       $penca = new Application_Model_Championships();
        
       $champs = $penca->load();
       
       $this->view->championships = $champs;
       
       
    }
    
    /**
     * Salva la rodada
     * 
     * @param champ
     * @param rodada
     */
    public function salvarrodadaAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);        
        
       if ($this->getRequest()->isPost()) {  
           $penca = new Application_Model_Championships();
           $penca->salvar_rodada($params['champ'], $params['rodada']);
           
       }
       
       $this->getResponse()
            ->setHeader('Content-Type', 'application/json');
      
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($params);
    }
    
    public function rodadaatualAction() { 
       $params = $this->_request->getParams(); 
        
       $penca = new Application_Model_Championships();
        
       $champs = $penca->load();
       
       $this->view->championships = $champs;
       
       if (!empty($params['champ'])) {
           
           $this->view->rondas = $penca->getrondas($params['champ']);
            $this->view->champ = $params['champ'];    
       } 
    }
    
    public function setrodadaatualAction() {
        $params = $this->_request->getParams();
        
//        print_r($params);
//        die(".");
        
        $penca = new Application_Model_Championships();
        
        $penca->setRondaAtual($params['champ_selected'], $params['ronda']);
        
        $this->render("rodadaatual");
    }

    /**
     * Seta la rodada como actual en el campeonato
     * 
     * @param champ_selected
     * @param ronda
     */
    public function setrodadaAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
         
        $campeonato = new Application_Model_Championships();        
        $campeonato->setRondaAtual($params['champ_selected'], $params['ronda']);
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
}

